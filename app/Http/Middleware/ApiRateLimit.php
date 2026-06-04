<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Apply rate limiting for API endpoints
        if ($request->is('api/*')) {
            $userId = $request->user() ? $request->user()->id : null;
            $key = 'api:' . ($userId ?: $request->ip());

            if (RateLimiter::tooManyAttempts($key, 60)) { // 60 requests per minute
                return response()->json([
                    'message' => 'Too many requests. Please try again later.'
                ], 429);
            }

            RateLimiter::hit($key);
        }

        return $next($request);
    }
}
