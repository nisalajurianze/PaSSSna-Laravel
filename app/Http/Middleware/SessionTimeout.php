<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (Auth::check()) {
            $lastActivity = session('last_activity');
            $currentTime = Carbon::now();
            $user = Auth::user();

            // Set default timeout (30 minutes for customer, 15 for admin)
            $timeoutMinutes = ($user && $user->role === 'admin') ? 15 : 30;

            if ($lastActivity && $currentTime->diffInMinutes(Carbon::parse($lastActivity)) > $timeoutMinutes) {
                // Store current URL for redirect after login
                session(['url.intended' => $request->fullUrl()]);

                // Logout user
                Auth::logout();
                session()->flush();

                return redirect()->route('login')->with('error', 'Your session has expired due to inactivity. Please login again.');
            }

            // Update last activity time
            session(['last_activity' => $currentTime->toDateTimeString()]);
        }

        return $next($request);
    }
}
