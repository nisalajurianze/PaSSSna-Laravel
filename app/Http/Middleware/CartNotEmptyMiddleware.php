<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CartNotEmptyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get cart from session
        $cart = session()->get('cart', []);

        // Check if cart is empty
        if (empty($cart)) {
            // Different messages for different pages
            if ($request->is('checkout*')) {
                return redirect()->route('cart')->with('error', 'Your cart is empty. Please add items before checkout.');
            }
            if ($request->is('cart')) {
                return redirect()->route('menu')->with('info', 'Your cart is empty. Browse our menu to add items.');
            }
        }

        return $next($request);
    }
}
