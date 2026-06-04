<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has any of the required roles
        $userRole = auth()->user()->role;

        if (!in_array($userRole, $roles)) {
            // Redirect based on user role
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.');
            } else {
                return redirect()->route('customer.dashboard')->with('error', 'Unauthorized access.');
            }
        }

        return $next($request);
    }
}
