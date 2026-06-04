<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Promotion;
use App\Models\User;

class ValidatePromoCode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('checkout') && $request->method() === 'POST') {
            if ($request->has('promo_code') && !empty($request->promo_code)) {
                $promoCode = $request->promo_code;

                // Validate promo code
                $promotion = Promotion::where('code', $promoCode)
                    ->where('is_active', true)
                    ->where(function($query) {
                        $query->whereNull('valid_until')
                              ->orWhere('valid_until', '>=', now());
                    })
                    ->first();

                if (!$promotion) {
                    return back()->withErrors([
                        'promo_code' => 'Invalid or expired promo code.'
                    ])->withInput();
                }

                // Check if promo code has usage limit
                if ($promotion->usage_limit > 0 && $promotion->times_used >= $promotion->usage_limit) {
                    return back()->withErrors([
                        'promo_code' => 'This promo code has reached its usage limit.'
                    ])->withInput();
                }

                // Check if user has already used this promo code
                if ($promotion->once_per_user) {
                    /** @var User|null $user */
                    $user = Auth::user();
                    if ($user && $user->orders()->where('promo_code', $promoCode)->exists()) {
                        return back()->withErrors([
                            'promo_code' => 'You have already used this promo code.'
                        ])->withInput();
                    }
                }

                // Store validated promo code in session
                session(['validated_promo_code' => $promotion]);
            }
        }

        return $next($request);
    }
}
