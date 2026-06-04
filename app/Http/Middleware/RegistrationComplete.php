<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RegistrationComplete
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated customers
        if (Auth::check() && Auth::user()->role === 'customer') {
            $user = Auth::user();

            // Check if required profile information is complete
            $requiredFields = ['phone', 'address'];
            $incompleteFields = [];

            foreach ($requiredFields as $field) {
                if (empty($user->$field)) {
                    $incompleteFields[] = $field;
                }
            }

            // If profile is incomplete and trying to checkout
            if (!empty($incompleteFields) && $request->is('checkout')) {
                return redirect()->route('customer.profile')
                    ->with('warning', 'Please complete your profile information before checkout.')
                    ->with('incomplete_fields', $incompleteFields);
            }

            // If profile is incomplete, show warning on dashboard
            if (!empty($incompleteFields) && $request->is('customer/dashboard')) {
                session()->flash('profile_incomplete', [
                    'message' => 'Your profile is incomplete. Please update your information.',
                    'fields' => $incompleteFields
                ]);
            }
        }

        return $next($request);
    }
}
