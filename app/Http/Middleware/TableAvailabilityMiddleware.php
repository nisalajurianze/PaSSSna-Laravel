<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;

class TableAvailabilityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('reservation') && $request->method() === 'POST') {
            $reservationDate = Carbon::parse($request->reservation_date);
            $reservationTime = Carbon::parse($request->reservation_time);
            $people = $request->people;
            $selectedTables = $request->tables ?? [];

            // Check if tables are selected
            if (empty($selectedTables)) {
                return back()->withErrors([
                    'tables' => 'Please select at least one table.'
                ])->withInput();
            }

            // Validate each selected table
            foreach ($selectedTables as $tableId) {
                $table = Table::find($tableId);

                if (!$table) {
                    return back()->withErrors([
                        'tables' => 'Invalid table selected.'
                    ])->withInput();
                }

                // Check if table is available
                if (!$table->is_available) {
                    return back()->withErrors([
                        'tables' => "Table {$table->table_number} is currently not available."
                    ])->withInput();
                }

                // Check for overlapping reservations (2-hour window)
                $reservationEndTime = $reservationTime->copy()->addHours(2);
                $existingReservation = Reservation::where('table_id', $tableId)
                    ->where('reservation_date', $reservationDate->format('Y-m-d'))
                    ->where('status', 'confirmed')
                    ->where('reservation_time', '<', $reservationEndTime)
                    ->where('reservation_time', '>', $reservationTime->copy()->subHours(2))
                    ->exists();

                if ($existingReservation) {
                    return back()->withErrors([
                        'tables' => "Table {$table->table_number} is already booked for this time."
                    ])->withInput();
                }
            }
        }

        return $next($request);
    }
}
