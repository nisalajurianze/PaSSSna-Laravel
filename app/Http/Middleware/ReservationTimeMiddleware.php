<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class ReservationTimeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for reservation creation
        if ($request->is('reservation') && $request->method() === 'POST') {
            $request->validate([
                'reservation_date' => 'required|date',
                'reservation_time' => 'required',
                'people' => 'required|integer|min:1|max:20',
            ]);

            $reservationDate = Carbon::parse($request->reservation_date);
            $reservationTime = Carbon::parse($request->reservation_time);

            // Check if reservation is at least 1 hour in advance
            $now = Carbon::now();
            $reservationDateTime = $reservationDate->copy()->setTime($reservationTime->hour, $reservationTime->minute);

            if ($reservationDateTime->diffInHours($now) < 1) {
                return back()->withErrors([
                    'reservation_time' => 'Reservations must be made at least 1 hour in advance.'
                ])->withInput();
            }

            // Check if reservation is within opening hours
            $hour = $reservationTime->hour;
            $dayOfWeek = $reservationDate->dayOfWeek;

            // Restaurant opening hours
            $openingHours = [
                0 => ['open' => 12, 'close' => 21], // Sunday
                1 => ['open' => 11, 'close' => 22], // Monday
                2 => ['open' => 11, 'close' => 22], // Tuesday
                3 => ['open' => 11, 'close' => 22], // Wednesday
                4 => ['open' => 11, 'close' => 22], // Thursday
                5 => ['open' => 11, 'close' => 23], // Friday
                6 => ['open' => 11, 'close' => 23], // Saturday
            ];

            if (!isset($openingHours[$dayOfWeek]) ||
                $hour < $openingHours[$dayOfWeek]['open'] ||
                $hour >= $openingHours[$dayOfWeek]['close']) {
                return back()->withErrors([
                    'reservation_time' => 'Reservation time is outside of opening hours.'
                ])->withInput();
            }
        }

        return $next($request);
    }
}
