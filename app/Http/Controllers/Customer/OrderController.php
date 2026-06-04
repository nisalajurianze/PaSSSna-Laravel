<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with('items')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items', 'payment']);

        return view('customer.orders.show', compact('order'));
    }

    public function details(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order->load('items');

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'total_amount' => $order->total,
            'items' => $order->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name ?? $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            })->values(),
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'cancellation_reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function invoice(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return redirect()->route('pdf.invoice', $order);
    }

    public function showCart()
    {
        $cart = session()->get('cart', []);
        $subtotal = $this->calculateSubtotal($cart);

        // Get available promotions
        $promotions = Promotion::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->get();

        return view('customer.cart', compact('cart', 'subtotal', 'promotions'));
    }

    public function updateCart(Request $request, $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
            'action' => 'nullable|in:update,remove'
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$key])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.'
            ]);
        }

        if ($request->action == 'remove') {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $request->quantity;
            $cart[$key]['total'] = $cart[$key]['price'] * $request->quantity;
        }

        session()->put('cart', $cart);

        $subtotal = $this->calculateSubtotal($cart);
        $tax = $subtotal * 0.08;
        $total = $subtotal + $tax;

        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
            'cart_total' => $total,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'message' => 'Cart updated successfully.'
        ]);
    }

    public function removeFromCart($key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'cart_count' => count($cart),
                'message' => 'Item removed from cart.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart.'
        ]);
    }

    public function clearCart()
    {
        session()->forget('cart');
        session()->forget('promo_code');
        session()->forget('discount');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully.'
        ]);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('menu')
                ->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $subtotal = $this->calculateSubtotal($cart);
        $tax = $subtotal * 0.08;

        // Get promo discount if applied
        $discount = session()->get('discount', 0);
        $promoCode = session()->get('promo_code');

        // Calculate delivery charge
        $deliveryCharge = 5.00; // Default delivery charge
        $total = $subtotal + $tax + $deliveryCharge - $discount;

        // Get user's saved addresses
        // Note: addresses() relationship not implemented in User model
        // $savedAddresses = $user->addresses()->get();
        $savedAddresses = [];

        // Get available time slots for delivery/takeaway
        $timeSlots = $this->generateTimeSlots();

        return view('customer.checkout', compact(
            'cart',
            'user',
            'subtotal',
            'tax',
            'deliveryCharge',
            'discount',
            'promoCode',
            'total',
            'savedAddresses',
            'timeSlots'
        ));
    }

    public function applyPromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $promotion = Promotion::where('code', $request->code)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->first();

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired promo code.'
            ]);
        }

        // Check total usage limit
        if ($promotion->total_usage_limit &&
            $promotion->orders()->count() >= $promotion->total_usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code usage limit reached.'
            ]);
        }

        // Check per user usage limit
        if ($promotion->max_usage_per_user && Auth::check()) {
            $userUsage = $promotion->orders()
                ->where('user_id', Auth::id())
                ->count();

            if ($userUsage >= $promotion->max_usage_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this promo code.'
                ]);
            }
        }

        // Calculate cart subtotal for validation
        $cart = session()->get('cart', []);
        $subtotal = $this->calculateSubtotal($cart);

        // Check minimum order amount
        if ($promotion->min_order_amount && $subtotal < $promotion->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order amount not met.'
            ]);
        }

        // Calculate discount
        $discount = $this->calculateDiscount($promotion, $subtotal);

        // Save to session
        session()->put('promo_code', $promotion->code);
        session()->put('discount', $discount);
        session()->put('promotion_id', $promotion->id);

        $tax = $subtotal * 0.08;
        $deliveryCharge = 5.00;
        $total = $subtotal + $tax + $deliveryCharge - $discount;

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'total' => $total,
            'message' => 'Promo code applied successfully.',
            'promotion' => [
                'title' => $promotion->title,
                'description' => $promotion->description
            ]
        ]);
    }

    public function removePromoCode()
    {
        session()->forget('promo_code');
        session()->forget('discount');
        session()->forget('promotion_id');

        return response()->json([
            'success' => true,
            'message' => 'Promo code removed.'
        ]);
    }

    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|in:delivery,takeaway',
            'delivery_address' => 'required_if:order_type,delivery',
            'delivery_time' => 'required|date_format:Y-m-d H:i:s|after:now',
            'payment_method' => 'required|in:cash,card,cash_on_delivery',
            'card_number' => 'required_if:payment_method,card',
            'card_expiry' => 'required_if:payment_method,card',
            'card_cvc' => 'required_if:payment_method,card',
            'name_on_card' => 'required_if:payment_method,card',
            'special_instructions' => 'nullable|string',
            'save_address' => 'boolean',
            'save_card' => 'boolean'
        ]);

        // Validate cash on delivery only for delivery orders
        if ($validated['payment_method'] == 'cash_on_delivery' && $validated['order_type'] != 'delivery') {
            return back()->with('error', 'Cash on delivery is only available for delivery orders.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $subtotal = $this->calculateSubtotal($cart);
            $tax = $subtotal * 0.08;
            $deliveryCharge = $validated['order_type'] == 'delivery' ? 5.00 : 0;
            $discount = session()->get('discount', 0);
            $total = $subtotal + $tax + $deliveryCharge - $discount;

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'order_type' => $validated['order_type'],
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'delivery_charge' => $deliveryCharge,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] == 'card' ? 'paid' : 'pending',
                'delivery_address' => $validated['delivery_address'] ?? null,
                'estimated_delivery_time' => $validated['delivery_time'],
                'customer_phone' => $user->phone,
                'special_instructions' => $validated['special_instructions'],
                'promo_code' => session()->get('promo_code')
            ]);

            // Add order items
            foreach ($cart as $item) {
                $order->items()->create([
                    'menu_item_id' => $item['item_id'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'size' => $item['size'],
                    'flavor' => $item['flavor'] ?? null,
                    'toppings' => $item['toppings'] ?? [],
                    'special_instructions' => $item['special_instructions'] ?? null
                ]);
            }

            // Record promotion usage
            if (session()->has('promotion_id')) {
                $order->promotions()->attach(session()->get('promotion_id'), [
                    'discount_amount' => $discount
                ]);
            }

            // Save address if requested
            // Note: addresses() relationship not implemented in User model
            if ($request->save_address && !empty($validated['delivery_address'])) {
                // $user->addresses()->create([
                //     'address' => $validated['delivery_address'],
                //     'is_default' => $user->addresses()->count() == 0
                // ]);
            }

            // Save card if requested (in real app, use payment processor)
            if ($request->save_card && $validated['payment_method'] == 'card') {
                $lastFour = substr($validated['card_number'], -4);
                /** @var \App\Models\User $user */
                $user = Auth::user();
                $user->update([
                    'payment_card_last_four' => $lastFour,
                    'payment_card_expiry' => $validated['card_expiry'],
                    'payment_card_type' => $this->detectCardType($validated['card_number'])
                ]);
            }

            // Clear cart and session data
            session()->forget('cart');
            session()->forget('promo_code');
            session()->forget('discount');
            session()->forget('promotion_id');

            DB::commit();

            // Send confirmation email
            if ($user->email) {
                // Mail::to($user->email)->send(new OrderConfirmationMail($order));
            }

            return redirect()->route('order.confirmation', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function confirmation($id)
    {
        $order = Order::with(['items.menuItem', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Calculate estimated delivery time
        $preparationTime = $order->items->map(fn($item) => $item->menuItem->preparation_time ?? 15)->max() ?? 15;

        $estimatedTime = $order->created_at->addMinutes($preparationTime + 30); // +30 mins for delivery

        return view('customer.order-confirmation', compact('order', 'estimatedTime'));
    }

    public function generatePDF($id)
    {
        $order = Order::with(['items.menuItem', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $pdf = PDF::loadView('pdf.order', compact('order'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("invoice-{$order->order_number}.pdf");
    }

    public function trackOrder($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->findOrFail($id);

        $statusHistory = [
            'pending' => 'Order received',
            'confirmed' => 'Order confirmed',
            'preparing' => 'Food being prepared',
            'ready' => 'Food is ready',
            'out_for_delivery' => 'Out for delivery',
            'delivered' => 'Delivered',
            'completed' => 'Order completed'
        ];

        $currentStatus = $order->status;
        $statusIndex = array_search($currentStatus, array_keys($statusHistory));

        return response()->json([
            'success' => true,
            'order' => $order,
            'status_history' => $statusHistory,
            'current_status' => $currentStatus,
            'status_index' => $statusIndex,
            'estimated_delivery_time' => $order->estimated_delivery_time
        ]);
    }

    private function calculateSubtotal($cart)
    {
        return array_sum(array_column($cart, 'total'));
    }

    private function calculateDiscount($promotion, $subtotal)
    {
        switch ($promotion->type) {
            case 'percentage':
                $discount = ($subtotal * $promotion->discount_value) / 100;
                if ($promotion->max_discount && $discount > $promotion->max_discount) {
                    $discount = $promotion->max_discount;
                }
                break;

            case 'fixed_amount':
                $discount = min($promotion->discount_value, $subtotal);
                break;

            case 'free_shipping':
                $discount = 5.00; // Default delivery charge
                break;

            default:
                $discount = 0;
        }

        return round($discount, 2);
    }

    private function generateOrderNumber()
    {
        return Order::generateUniqueOrderNumber();
    }

    private function generateTimeSlots()
    {
        $slots = [];
        $now = Carbon::now();
        $start = $now->copy()->addMinutes(30)->ceilMinute(30); // Next half hour
        $end = $now->copy()->addHours(4); // 4 hours ahead

        while ($start <= $end) {
            $slots[] = $start->format('Y-m-d H:i:s');
            $start->addMinutes(30);
        }

        return $slots;
    }

    private function detectCardType($cardNumber)
    {
        $firstDigit = substr($cardNumber, 0, 1);

        switch ($firstDigit) {
            case '4':
                return 'visa';
            case '5':
                return 'mastercard';
            case '3':
                return 'amex';
            case '6':
                return 'discover';
            default:
                return 'other';
        }
    }

    /**
     * Check if orders have been updated (for real-time polling)
     */
    public function checkUpdated(Request $request)
    {
        $lastCheck = $request->get('last_check', now()->subMinutes(1)->toIso8601String());

        // Get user's recent orders (pending, confirmed, preparing, ready, out_for_delivery)
        $activeOrders = Order::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery'])
            ->where('updated_at', '>', $lastCheck)
            ->first();

        if ($activeOrders) {
            return response()->json([
                'updated' => true,
                'orderId' => $activeOrders->id,
                'orderNumber' => $activeOrders->order_number,
                'status' => $activeOrders->status,
                'timestamp' => now()->toIso8601String()
            ]);
        }

        return response()->json([
            'updated' => false,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
