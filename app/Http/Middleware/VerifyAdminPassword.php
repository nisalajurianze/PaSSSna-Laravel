<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

class VerifyAdminPassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is a dining exit request
        if ($request->is('customer/dining/exit') && $request->method() === 'POST') {
            $request->validate([
                'admin_password' => 'required|string'
            ]);

            // Verify admin password
            $adminPassword = 'PaSSSna_log'; // Hardcoded admin password
            $inputPassword = $request->admin_password;

            // Verify password - compare input with the hardcoded password using direct comparison
            // Note: In production, store the hashed password in config/env and verify against it
            if ($inputPassword !== $adminPassword) {
                return back()->with('error', 'Invalid admin password. Please try again.');
            }

            // Store verification in session
            session(['admin_password_verified' => true]);
        }

        return $next($request);
    }
}
