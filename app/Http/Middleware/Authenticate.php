<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Store intended URL for redirect after login
        if (!$request->expectsJson()) {
            session(['url.intended' => $request->fullUrl()]);
        }

        return $request->expectsJson() ? null : route('login');
    }
}
