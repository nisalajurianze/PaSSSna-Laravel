<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function generateTimeSlots(Carbon $date): array
    {
        $openingHours = config('restaurant.opening_hours', []);
        $slotInterval = (int) config('restaurant.reservation.slot_interval_minutes', 30);

        $dayOfWeek = $date->dayOfWeek;
        $openHour = data_get($openingHours, "{$dayOfWeek}.open");
        $closeHour = data_get($openingHours, "{$dayOfWeek}.close");

        if ($openHour === null || $closeHour === null) {
            return [];
        }

        $start = $date->copy()->setTime((int) $openHour, 0, 0);
        $end = $date->copy()->setTime((int) $closeHour, 0, 0);

        $slots = [];
        $cursor = $start->copy();
        while ($cursor->lt($end)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes($slotInterval);
        }

        return $slots;
    }

    public function getAvailableTables(
        string $date,
        string $time,
        int $guests,
        int $durationMinutes,
        ?int $excludeReservationId = null
    ): Collection {
        $startAt = Carbon::parse("{$date} {$time}");
        $endAt = $startAt->copy()->addMinutes($durationMinutes);

        $conflictingTableIds = DB::table('reservation_table')
            ->join('reservations', 'reservation_table.reservation_id', '=', 'reservations.id')
            ->whereIn('reservations.status', [
                Reservation::STATUS_PENDING,
                Reservation::STATUS_CONFIRMED,
                Reservation::STATUS_SEATED,
            ])
            ->when($excludeReservationId, function ($q) use ($excludeReservationId) {
                $q->where('reservations.id', '!=', $excludeReservationId);
            })
            ->where('reservations.start_at', '<', $endAt)
            ->where('reservations.end_at', '>', $startAt)
            ->pluck('reservation_table.table_id')
            ->unique()
            ->values();

        return Table::query()
            ->where('is_active', true)
            ->where('status', Table::STATUS_AVAILABLE)
            ->when($guests > 0, function ($q) {
                // Keep query stable; capacity validation happens when selecting tables.
                return $q;
            })
            ->when($conflictingTableIds->isNotEmpty(), function ($q) use ($conflictingTableIds) {
                $q->whereNotIn('id', $conflictingTableIds->all());
            })
            ->orderBy('capacity')
            ->orderBy('table_number')
            ->get();
    }

    /**
     * Find the best combination of tables to fit a party.
     * Preference order: (1) fewest tables, (2) least unused seats.
     */
    public function findBestTableCombination(Collection $availableTables, int $guests, int $maxTables = 4): array
    {
        $tables = $availableTables->sortBy('capacity')->values();
        $count = $tables->count();

        if ($guests <= 0 || $count === 0) {
            return [];
        }

        $best = null;

        $maxTables = min($maxTables, $count);
        for ($k = 1; $k <= $maxTables; $k++) {
            $candidate = $this->bestComboOfK($tables, $guests, $k);
            if ($candidate !== null) {
                $best = $candidate;
                break;
            }
        }

        return $best ? $best['table_ids'] : [];
    }

    public function validateSelection(array $tableIds, int $guests): void
    {
        $tables = Table::whereIn('id', $tableIds)->get();

        if ($tables->count() !== count($tableIds)) {
            throw new \InvalidArgumentException('One or more selected tables are invalid.');
        }

        $capacity = (int) $tables->sum('capacity');
        if ($capacity < $guests) {
            throw new \InvalidArgumentException('Selected tables do not have enough capacity.');
        }
    }

    private function bestComboOfK(Collection $tables, int $guests, int $k): ?array
    {
        $best = null;

        $this->comboBacktrack(
            $tables,
            $k,
            0,
            [],
            0,
            $guests,
            $best
        );

        return $best;
    }

    private function comboBacktrack(
        Collection $tables,
        int $k,
        int $startIndex,
        array $currentIds,
        int $currentCapacity,
        int $guests,
        ?array &$best
    ): void {
        if (count($currentIds) === $k) {
            if ($currentCapacity >= $guests) {
                $unused = $currentCapacity - $guests;
                if ($best === null || $unused < $best['unused']) {
                    $best = [
                        'table_ids' => $currentIds,
                        'unused' => $unused,
                    ];
                }
            }
            return;
        }

        $remainingSlots = $k - count($currentIds);
        $remainingTables = $tables->count() - $startIndex;
        if ($remainingTables < $remainingSlots) {
            return;
        }

        for ($i = $startIndex; $i < $tables->count(); $i++) {
            $table = $tables[$i];
            $nextIds = [...$currentIds, $table->id];
            $nextCapacity = $currentCapacity + (int) $table->capacity;

            // Simple pruning: if we already found a perfect fit, stop.
            if ($best !== null && $best['unused'] === 0) {
                return;
            }

            $this->comboBacktrack(
                $tables,
                $k,
                $i + 1,
                $nextIds,
                $nextCapacity,
                $guests,
                $best
            );
        }
    }

    // --- Reporting helpers (used by ReportService/Admin dashboard) ---

    public function getReservationStatistics(string $period = 'today'): array
    {
        $query = Reservation::query();

        switch ($period) {
            case 'today':
                $query->whereDate('reservation_date', today());
                break;
            case 'week':
                $query->whereBetween('reservation_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('reservation_date', now()->month)
                    ->whereYear('reservation_date', now()->year);
                break;
            case 'year':
                $query->whereYear('reservation_date', now()->year);
                break;
            default:
                // Custom range is handled elsewhere
                break;
        }

        $total = (clone $query)->count();
        $confirmed = (clone $query)->where('status', Reservation::STATUS_CONFIRMED)->count();
        $seated = (clone $query)->where('status', Reservation::STATUS_SEATED)->count();
        $cancelled = (clone $query)->where('status', Reservation::STATUS_CANCELLED)->count();
        $noShow = (clone $query)->where('status', Reservation::STATUS_NO_SHOW)->count();

        return [
            'total' => $total,
            'confirmed' => $confirmed,
            'seated' => $seated,
            'cancelled' => $cancelled,
            'no_show' => $noShow,
            'confirmation_rate' => $total > 0 ? ($confirmed / $total) * 100 : 0,
            'cancellation_rate' => $total > 0 ? ($cancelled / $total) * 100 : 0,
        ];
    }

    public function getTableUtilization(string $startDate, string $endDate): array
    {
        $tables = Table::where('is_active', true)->get();
        $utilization = [];

        foreach ($tables as $table) {
            $reservations = $table->reservations()
                ->whereBetween('reservation_date', [$startDate, $endDate])
                ->where('status', Reservation::STATUS_COMPLETED)
                ->get();

            $totalMinutes = (int) $reservations->sum(function (Reservation $r) {
                return (int) ($r->duration_minutes ?? 0);
            });

            $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $totalPossibleMinutes = $days * 12 * 60;

            $utilizationRate = $totalPossibleMinutes > 0 ? ($totalMinutes / $totalPossibleMinutes) * 100 : 0;

            $utilization[] = [
                'table' => $table->full_name,
                'total_reservations' => $reservations->count(),
                'total_minutes' => $totalMinutes,
                'utilization_rate' => $utilizationRate,
                'average_duration' => $reservations->count() > 0 ? $totalMinutes / $reservations->count() : 0,
            ];
        }

        usort($utilization, fn($a, $b) => $b['utilization_rate'] <=> $a['utilization_rate']);

        return $utilization;
    }
}

