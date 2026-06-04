<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    public function create(ReservationService $reservationService)
    {
        $availableTables = Table::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('table_number')
            ->get();

        $maxCapacity = (int) $availableTables->sum('capacity');

        $previousReservation = null;
        if (Auth::check()) {
            $previousReservation = Reservation::where('user_id', Auth::id())
                ->latest()
                ->first();
        }

        $today = Carbon::today();
        $timeSlots = $reservationService->generateTimeSlots($today);

        return view('customer.reservation', compact(
            'timeSlots',
            'maxCapacity',
            'previousReservation',
            'availableTables'
        ));
    }

    /**
     * Get available time slots for a date (AJAX API).
     */
    public function getAvailableTimes(Request $request, ReservationService $reservationService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'guests' => 'nullable|integer|min:1|max:' . (int) config('restaurant.reservation.max_party_size', 20),
            'duration_minutes' => 'nullable|integer|min:30|max:360',
        ]);

        $date = Carbon::parse($validated['date']);
        $guests = (int) ($validated['guests'] ?? 0);
        $durationMinutes = (int) ($validated['duration_minutes'] ?? config('restaurant.reservation.duration_minutes', 90));

        $slots = $reservationService->generateTimeSlots($date);

        $times = [];
        foreach ($slots as $slot) {
            $available = true;

            if ($guests > 0) {
                $tables = $reservationService->getAvailableTables(
                    $date->toDateString(),
                    $slot,
                    $guests,
                    $durationMinutes
                );

                $available = ((int) $tables->sum('capacity')) >= $guests;
            }

            $times[] = [
                'time' => $slot,
                'available' => $available,
            ];
        }

        return response()->json([
            'success' => true,
            'times' => $times,
        ]);
    }

    /**
     * Get available tables for a given date/time (AJAX API).
     */
    public function getAvailableTables(Request $request, ReservationService $reservationService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'guests' => 'required|integer|min:1|max:' . (int) config('restaurant.reservation.max_party_size', 20),
            'duration_minutes' => 'nullable|integer|min:30|max:360',
        ]);

        $durationMinutes = (int) ($validated['duration_minutes'] ?? config('restaurant.reservation.duration_minutes', 90));

        $tables = $reservationService->getAvailableTables(
            $validated['date'],
            $validated['time'],
            (int) $validated['guests'],
            $durationMinutes
        );

        $suggested = $reservationService->findBestTableCombination($tables, (int) $validated['guests']);

        return response()->json([
            'success' => true,
            'tables' => $tables->map(function (Table $t) {
                return [
                    'id' => $t->id,
                    'table_number' => $t->table_number,
                    'capacity' => (int) $t->capacity,
                    'status' => $t->status,
                ];
            })->values(),
            'suggested_table_ids' => $suggested,
        ]);
    }

    public function checkAvailability(Request $request, ReservationService $reservationService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'guests' => 'required|integer|min:1|max:' . (int) config('restaurant.reservation.max_party_size', 20),
            'duration_minutes' => 'nullable|integer|min:30|max:360',
        ]);

        $durationMinutes = (int) ($validated['duration_minutes'] ?? config('restaurant.reservation.duration_minutes', 90));

        $tables = $reservationService->getAvailableTables(
            $validated['date'],
            $validated['time'],
            (int) $validated['guests'],
            $durationMinutes
        );

        $available = ((int) $tables->sum('capacity')) >= (int) $validated['guests'];

        return response()->json([
            'success' => true,
            'available' => $available,
            'tables' => $tables,
        ]);
    }

    public function store(Request $request, ReservationService $reservationService)
    {
        $maxAdvanceDays = (int) config('restaurant.reservation.max_advance_booking_days', 30);
        $maxPartySize = (int) config('restaurant.reservation.max_party_size', 20);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'guests' => "required|integer|min:1|max:{$maxPartySize}",
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays($maxAdvanceDays)->toDateString(),
            'time' => 'required|date_format:H:i',
            'tables' => 'required|array|min:1',
            'tables.*' => 'exists:tables,id',
            'special_requests' => 'nullable|string|max:1000',
            'terms' => 'accepted',
        ]);

        $durationMinutes = (int) config('restaurant.reservation.duration_minutes', 90);
        $startAt = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $endAt = $startAt->copy()->addMinutes($durationMinutes);

        $minAdvanceHours = (int) config('restaurant.reservation.min_advance_booking_hours', 2);
        if ($startAt->lt(now()->addHours($minAdvanceHours))) {
            return back()
                ->with('error', "Reservations must be made at least {$minAdvanceHours} hour(s) in advance.")
                ->withInput();
        }

        // Validate table capacity + availability for the selected time.
        $selectedTableIds = array_map('intval', $validated['tables']);
        $reservationService->validateSelection($selectedTableIds, (int) $validated['guests']);

        $availableTables = $reservationService->getAvailableTables(
            $validated['date'],
            $validated['time'],
            (int) $validated['guests'],
            $durationMinutes
        );

        $availableIds = $availableTables->pluck('id')->map(fn($id) => (int) $id)->all();
        foreach ($selectedTableIds as $tableId) {
            if (!in_array((int) $tableId, $availableIds, true)) {
                return back()
                    ->with('error', 'One or more selected tables are not available at the requested time.')
                    ->withInput();
            }
        }

        $reservationNumber = $this->generateReservationNumber();
        $tables = Table::whereIn('id', $selectedTableIds)->orderBy('table_number')->get();

        $reservation = null;
        DB::transaction(function () use (
            $validated,
            $reservationNumber,
            $durationMinutes,
            $startAt,
            $endAt,
            $selectedTableIds,
            $tables,
            &$reservation
        ) {
            $reservation = Reservation::create([
                'reservation_number' => $reservationNumber,
                'user_id' => Auth::check() ? Auth::id() : null,
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
                'reservation_date' => $validated['date'],
                'reservation_time' => $validated['time'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'duration_minutes' => $durationMinutes,
                'number_of_people' => (int) $validated['guests'],
                'table_numbers' => $tables->pluck('table_number')->values()->all(),
                'table_count' => $tables->count(),
                'status' => Reservation::STATUS_PENDING,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            $reservation->tables()->sync($selectedTableIds);
        });

        // Ensure reservation was created successfully
        if (!$reservation) {
            return back()
                ->with('error', 'Failed to create reservation. Please try again.')
                ->withInput();
        }

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'reservation' => $reservation->load('tables'),
            ]);
        }

        return redirect()
            ->route('reservation.success', $reservation->id)
            ->with('success', 'Your reservation request has been submitted! We will confirm it shortly.');
    }

    public function success($id)
    {
        $reservation = Reservation::with('tables')->findOrFail($id);

        if (Auth::check() && $reservation->user_id && $reservation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.reservations.success', compact('reservation'));
    }

    public function index(Request $request)
    {
        $query = Reservation::with('tables')
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        $reservations = $query
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->paginate(10);

        return view('customer.reservations.index', compact('reservations'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('tables');

        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.reservations.show', compact('reservation'));
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$reservation->canBeCancelled()) {
            return back()->with('error', 'This reservation cannot be cancelled.');
        }

        $reservation->update([
            'status' => Reservation::STATUS_CANCELLED,
            'cancellation_reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Your reservation has been cancelled.');
    }

    private function generateReservationNumber(): string
    {
        do {
            $number = 'RSV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Reservation::where('reservation_number', $number)->exists());

        return $number;
    }

    /**
     * Check if reservations have been updated (for real-time polling)
     */
    public function checkUpdated(Request $request)
    {
        $lastCheck = $request->get('last_check', now()->subMinutes(1)->toIso8601String());

        // Get user's recent reservations that are pending or confirmed
        $activeReservations = Reservation::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('updated_at', '>', $lastCheck)
            ->first();

        if ($activeReservations) {
            return response()->json([
                'updated' => true,
                'reservationId' => $activeReservations->id,
                'status' => $activeReservations->status,
                'reservation' => $activeReservations,
                'timestamp' => now()->toIso8601String()
            ]);
        }

        return response()->json([
            'updated' => false,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
