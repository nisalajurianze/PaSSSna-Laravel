<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Check if user is customer
        if (Auth::user()->role !== 'customer') {
            // If admin tries to access customer routes, redirect to admin dashboard
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('info', 'Admins cannot access customer panel.');
            }
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // Check if customer is active
        if (!Auth::user()->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact restaurant.');
        }

        return $next($request);
    }
}
