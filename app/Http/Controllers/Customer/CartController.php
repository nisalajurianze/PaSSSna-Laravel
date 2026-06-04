<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index()
    {
        // If user is viewing the cart, drop any direct-checkout state
        Session::forget('direct_checkout');

        $cart = $this->getCart();
        $subtotal = $this->getCartTotal($cart);
        $cartCount = $this->getCartCount($cart);
        $discount = Session::get('discount_amount', 0);

        // Calculate charges
        $taxRate = (float) config('restaurant.order.tax_rate', 8);
        $serviceChargeRate = (float) config('restaurant.order.service_charge_rate', 10);
        $deliveryCharge = 0; // No delivery charge for now

        $tax = ($subtotal - $discount) * ($taxRate / 100);
        $serviceCharge = ($subtotal - $discount) * ($serviceChargeRate / 100);
        $total = $subtotal - $discount + $tax + $serviceCharge;

        // Get available promotions
        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return view('customer.cart', compact('cart', 'subtotal', 'cartCount', 'promotions', 'discount', 'tax', 'deliveryCharge', 'total'));
    }

    /**
     * Direct checkout for a single item (Order Now)
     */
    public function directCheckout(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();
        $validated = $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string',
            'flavor' => 'nullable|string',
            'toppings' => 'nullable|array',
            'special_instructions' => 'nullable|string|max:500'
        ]);

        $item = MenuItem::findOrFail($validated['item_id']);

        if (!$item->is_available) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'This item is currently unavailable.']);
            }
            return back()->with('error', 'This item is currently unavailable.');
        }

        $price = $this->calculatePrice($item, $validated);
        $cartKey = $this->generateCartKey($item->id, $validated);

        $directCart = [
            $cartKey => [
                'item_id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $validated['quantity'],
                'price' => $price,
                'size' => $validated['size'] ?? null,
                'flavor' => $validated['flavor'] ?? null,
                'toppings' => $validated['toppings'] ?? [],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'image' => $item->image,
                'total' => $price * $validated['quantity']
            ]
        ];

        Session::put('direct_checkout', $directCart);

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => 'Ready for checkout.',
                'cart_count' => count($directCart),
                'cart_total' => array_sum(array_column($directCart, 'total'))
            ]);
        }

        return redirect()->route('checkout');
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();
        $validated = $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string',
            'flavor' => 'nullable|string',
            'toppings' => 'nullable|array',
            'special_instructions' => 'nullable|string|max:500'
        ]);

        $item = MenuItem::findOrFail($validated['item_id']);

        if (!$item->is_available) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'This item is currently unavailable.']);
            }
            return back()->with('error', 'This item is currently unavailable.');
        }

        // Calculate price
        $price = $this->calculatePrice($item, $validated);

        // Get current cart
        $cart = $this->getCart();
        $cartKey = $this->generateCartKey($item->id, $validated);

        // Add or update item
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $validated['quantity'];
            $cart[$cartKey]['total'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['price'];
        } else {
            $cart[$cartKey] = [
                'item_id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $validated['quantity'],
                'price' => $price,
                'size' => $validated['size'] ?? null,
                'flavor' => $validated['flavor'] ?? null,
                'toppings' => $validated['toppings'] ?? [],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'image' => $item->image,
                'total' => $price * $validated['quantity']
            ];
        }

        $this->saveCart($cart);

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => "{$item->name} added to cart!",
                'cart_count' => $this->getCartCount($cart),
                'cart_total' => $this->getCartTotal($cart)
            ]);
        }

        return back()->with('success', "{$item->name} added to cart!");
    }

    /**
     * Update cart item quantity with change (+1/-1)
     */
    public function updateQuantity(Request $request, $id)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();

        // Accept both query param and body param for quantity_change
        $quantityChange = $request->input('quantity_change');
        if ($quantityChange === null) {
            $quantityChange = $request->query('quantity_change');
        }

        if ($quantityChange === null) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'quantity_change parameter is required.'], 400);
            }
            return back()->with('error', 'quantity_change parameter is required.');
        }

        // Validate the quantity change value
        $quantityChange = (int)$quantityChange;
        if ($quantityChange < -10 || $quantityChange > 10) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'Invalid quantity change value.'], 400);
            }
            return back()->with('error', 'Invalid quantity change value.');
        }

        $cart = $this->getCart();

        if (!isset($cart[$id])) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'Item not found in cart.']);
            }
            return back()->with('error', 'Item not found in cart.');
        }

        $newQuantity = $cart[$id]['quantity'] + $quantityChange;

        if ($newQuantity <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['quantity'] = $newQuantity;
            $cart[$id]['total'] = $cart[$id]['quantity'] * $cart[$id]['price'];
        }

        $this->saveCart($cart);

        if ($wantsJson) {
            $response = [
                'success' => true,
                'message' => 'Cart updated.',
                'cart_count' => $this->getCartCount($cart),
                'cart_total' => $this->getCartTotal($cart),
                'removed' => !isset($cart[$id])
            ];

            if (isset($cart[$id])) {
                $response['item'] = [
                    'quantity' => $cart[$id]['quantity'],
                    'total' => $cart[$id]['total']
                ];
            }

            return response()->json($response);
        }

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Update cart item quantity (set exact quantity)
     */
    public function update(Request $request, $id)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $cart = $this->getCart();

        if (!isset($cart[$id])) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'Item not found in cart.']);
            }
            return back()->with('error', 'Item not found in cart.');
        }

        $cart[$id]['quantity'] = $validated['quantity'];
        $cart[$id]['total'] = $cart[$id]['quantity'] * $cart[$id]['price'];

        $this->saveCart($cart);

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'cart_count' => $this->getCartCount($cart),
                'cart_total' => $this->getCartTotal($cart)
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request, $id)
    {
        // Check if it's a DELETE request or POST with _method=DELETE
        $isDelete = $request->isMethod('delete') ||
                    ($request->isMethod('post') && $request->input('_method') === 'DELETE');

        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson() || $isDelete || $request->isMethod('post');

        $cart = $this->getCart();

        if (!isset($cart[$id])) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'Item not found in cart.'], 404);
            }
            return back()->with('error', 'Item not found in cart.');
        }

        unset($cart[$id]);
        $this->saveCart($cart);

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart_count' => $this->getCartCount($cart),
                'cart_total' => $this->getCartTotal($cart),
            ]);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all items from cart
     */
    public function clear(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson() || $request->isMethod('post');
        Session::forget('cart');
        Session::forget('promo_code');
        Session::forget('discount_amount');

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared.',
                'cart_count' => 0,
                'cart_total' => 0,
            ]);
        }

        return back()->with('success', 'Cart cleared.');
    }

    /**
     * Apply promo code
     */
    public function applyPromo(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();
        $validated = $request->validate([
            'promo_code' => 'required|string'
        ]);

        $promo = Promotion::where('promo_code', $validated['promo_code'])
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promo) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired promo code.']);
            }
            return back()->with('error', 'Invalid or expired promo code.');
        }

        // Check minimum order amount
        $cartTotal = $this->getCartTotal($this->getCart());
        if ($cartTotal < $promo->minimum_order_amount) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => "Minimum order amount of Rs. {$promo->minimum_order_amount} required."]);
            }
            return back()->with('error', "Minimum order amount of Rs. {$promo->minimum_order_amount} required.");
        }

        // Calculate discount
        $discount = 0;
        if ($promo->type === 'percentage') {
            $discount = $cartTotal * ((float) $promo->discount_value / 100);
        } elseif ($promo->type === 'fixed') {
            $discount = (float) $promo->discount_value;
        }

        $discount = min($discount, $cartTotal);

        Session::put('promo_code', $promo->promo_code);
        Session::put('discount_amount', $discount);

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => 'Promo code applied successfully!',
                'discount' => $discount,
                'cart_total' => $cartTotal - $discount
            ]);
        }

        return back()->with('success', 'Promo code applied successfully!');
    }

    /**
     * Remove promo code
     */
    public function removePromo(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();
        Session::forget('promo_code');
        Session::forget('discount_amount');

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => 'Promo code removed.',
                'cart_total' => $this->getCartTotal($this->getCart())
            ]);
        }

        return back()->with('success', 'Promo code removed.');
    }

    /**
     * Quick add to cart (AJAX)
     */
    public function quickAdd(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $item = MenuItem::findOrFail($validated['item_id']);

        if (!$item->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'This item is currently unavailable.'
            ]);
        }

        $cart = $this->getCart();
        $cartKey = $item->id . '_quick';

        $cart[$cartKey] = [
            'item_id' => $item->id,
            'name' => $item->name,
            'quantity' => $validated['quantity'],
            'price' => $item->price,
            'total' => $item->price * $validated['quantity']
        ];

        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'cart_count' => $this->getCartCount($cart),
            'cart_total' => $this->getCartTotal($cart)
        ]);
    }

    /**
     * Get cart summary
     */
    public function getSummary()
    {
        $cart = $this->getCart();
        $subtotal = $this->getCartTotal($cart);
        $discount = Session::get('discount_amount', 0);
        $deliveryCharge = (float) config('restaurant.order.delivery_charge', 300);
        $taxRate = (float) config('restaurant.order.tax_rate', 8);
        $serviceCharge = (float) config('restaurant.order.service_charge_rate', 10);

        $taxAmount = ($subtotal - $discount) * ($taxRate / 100);
        $total = $subtotal - $discount + $deliveryCharge + $taxAmount + ($subtotal * $serviceCharge / 100);

        return response()->json([
            'cart' => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'delivery_charge' => $deliveryCharge,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'service_charge' => $subtotal * $serviceCharge / 100,
            'total' => $total,
            'cart_count' => $this->getCartCount($cart)
        ]);
    }

    /**
     * Get cart from session
     */
    private function getCart()
    {
        return Session::get('cart', []);
    }

    /**
     * Save cart to session
     */
    private function saveCart($cart)
    {
        Session::put('cart', $cart);
    }

    /**
     * Get cart count
     */
    private function getCartCount($cart)
    {
        return count($cart);
    }

    /**
     * Get cart total
     */
    private function getCartTotal($cart)
    {
        return array_sum(array_column($cart, 'total'));
    }

    /**
     * Generate unique cart key
     */
    private function generateCartKey($itemId, $options)
    {
        $key = $itemId;
        $key .= '_' . ($options['size'] ?? 'regular');
        $key .= '_' . ($options['flavor'] ?? 'default');

        if (!empty($options['toppings'])) {
            sort($options['toppings']);
            $key .= '_' . implode(',', $options['toppings']);
        }

        return md5($key);
    }

    /**
     * Calculate item price
     */
    private function calculatePrice($item, $options)
    {
        $price = $item->current_price ?? $item->price;

        // Size pricing
        if (!empty($options['size']) && isset($item->sizes[$options['size']])) {
            $price = $item->sizes[$options['size']];
        }

        // Flavor pricing
        if (!empty($options['flavor']) && is_array($item->flavors)) {
            $flavorName = $options['flavor'];
            if (isset($item->flavors[$flavorName]) && is_numeric($item->flavors[$flavorName])) {
                $price += (float) $item->flavors[$flavorName];
            } else {
                foreach ($item->flavors as $flavor) {
                    if (is_array($flavor) && ($flavor['name'] ?? null) === $flavorName) {
                        $price += (float) ($flavor['price'] ?? 0);
                        break;
                    }
                }
            }
        }

        // Toppings pricing
        if (!empty($options['toppings'])) {
        foreach ($options['toppings'] as $topping) {
            if (isset($item->extra_toppings[$topping])) {
                $price += $item->extra_toppings[$topping];
            } elseif (is_array($item->extra_toppings)) {
                foreach ($item->extra_toppings as $extra) {
                    if (is_array($extra) && ($extra['name'] ?? null) === $topping) {
                        $price += (float) ($extra['price'] ?? 0);
                        break;
                    }
                }
            }
        }
        }

        return $price;
    }
}
