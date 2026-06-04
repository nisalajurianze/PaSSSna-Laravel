<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckRestaurantOpen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if restaurant is open for specific routes
        if ($this->shouldCheckOpenHours($request)) {
            $now = Carbon::now();
            $dayOfWeek = $now->dayOfWeek;
            $hour = $now->hour;

            // Restaurant opening hours (configurable)
            $openingHours = config('restaurant.opening_hours', [
                0 => ['open' => 12, 'close' => 21], // Sunday
                1 => ['open' => 11, 'close' => 22], // Monday
                2 => ['open' => 11, 'close' => 22], // Tuesday
                3 => ['open' => 11, 'close' => 22], // Wednesday
                4 => ['open' => 11, 'close' => 22], // Thursday
                5 => ['open' => 11, 'close' => 23], // Friday
                6 => ['open' => 11, 'close' => 23], // Saturday
            ]);

            // Check if restaurant is currently open
            if (!isset($openingHours[$dayOfWeek]) ||
                $hour < $openingHours[$dayOfWeek]['open'] ||
                $hour >= $openingHours[$dayOfWeek]['close']) {

                // Only show message for specific pages
                if ($request->is('menu*') || $request->is('reservation*') || $request->is('cart*')) {
                    session()->flash('restaurant_closed', [
                        'message' => 'Restaurant is currently closed. Our opening hours are:',
                        'hours' => $openingHours
                    ]);
                }

                // Prevent online ordering when closed (except reservations)
                if ($request->is('checkout') && $request->method() === 'POST') {
                    return back()->with('error', 'Online ordering is currently unavailable. Restaurant is closed.');
                }
            }
        }

        return $next($request);
    }

    /**
     * Determine if we should check restaurant opening hours for this request.
     */
    private function shouldCheckOpenHours(Request $request): bool
    {
        $routesToCheck = [
            'menu.*',
            'cart.*',
            'checkout.*',
            'reservation.*',
            'dining.*'
        ];

        foreach ($routesToCheck as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }
}
