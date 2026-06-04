<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminApiMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error_code' => 'UNAUTHENTICATED',
            ], 401);
        }

        $user = Auth::user();

        // Check if user has admin role
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Admin access required.',
                'error_code' => 'FORBIDDEN',
            ], 403);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated.',
                'error_code' => 'ACCOUNT_DEACTIVATED',
            ], 403);
        }

        return $next($request);
    }
}
