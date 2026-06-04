<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index()
    {
        $cart = $this->getCheckoutCart();
        $isDirect = $this->isDirectCheckout();

        if (empty($cart)) {
            return redirect()->route('menu')->with('error', 'Your cart is empty.');
        }

        $subtotal = $this->getCartTotal($cart);
        $discount = $isDirect ? 0 : Session::get('discount_amount', 0);

        $deliveryFee = (float) config('restaurant.order.delivery_charge', 0);
        $freeDeliveryThreshold = (float) config('restaurant.order.free_delivery_threshold', 0);
        $taxRate = (float) config('restaurant.order.tax_rate', 0);
        $serviceChargeRate = (float) config('restaurant.order.service_charge_rate', 0);

        $tax = max(0, ($subtotal - $discount)) * ($taxRate / 100);
        $serviceCharge = max(0, ($subtotal - $discount)) * ($serviceChargeRate / 100);

        // Default preview is takeaway (no delivery fee applied)
        $total = $subtotal - $discount + $tax + $serviceCharge;

        // Get available tables for dine-in
        $tables = Table::where('status', 'available')->get();

        return view('customer.checkout', compact(
            'cart',
            'subtotal',
            'discount',
            'deliveryFee',
            'freeDeliveryThreshold',
            'tax',
            'serviceCharge',
            'total',
            'tables',
            'isDirect'
        ));
    }

    /**
     * Process checkout
     */
    public function process(Request $request)
    {
        $data = $this->normalizeCheckoutInput($request);
        $validator = Validator::make($data, $this->validationRules(), $this->messages());

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cart = $this->getCheckoutCart();
        $isDirect = $this->isDirectCheckout();

        if (empty($cart)) {
            return redirect()->route('menu')->with('error', 'Your cart is empty.');
        }

        $validated = $validator->validated();

        // Calculate totals
        $subtotal = $this->getCartTotal($cart);
        $discount = $isDirect ? 0 : Session::get('discount_amount', 0);
        $taxRate = (float) config('restaurant.order.tax_rate', 0);
        $serviceChargeRate = (float) config('restaurant.order.service_charge_rate', 0);
        $deliveryFee = (float) config('restaurant.order.delivery_charge', 0);
        $freeDeliveryThreshold = (float) config('restaurant.order.free_delivery_threshold', 0);

        $orderType = $validated['delivery_type'];
        $paymentMethod = $validated['payment_method'];

        // Only allow cash on delivery for delivery orders
        if ($orderType === 'delivery' && $paymentMethod === 'cash') {
            $paymentMethod = Order::PAYMENT_COD;
        }

        if ($orderType !== 'delivery' && $paymentMethod === Order::PAYMENT_COD) {
            return back()->with('error', 'Cash on Delivery is only available for delivery orders.')->withInput();
        }

        $deliveryCharge = 0.0;
        if ($orderType === 'delivery') {
            $deliveryCharge = ($freeDeliveryThreshold > 0 && $subtotal >= $freeDeliveryThreshold) ? 0.0 : $deliveryFee;
        }

        $taxable = max(0, ($subtotal - $discount));
        $taxAmount = $taxable * ($taxRate / 100);
        $serviceAmount = $taxable * ($serviceChargeRate / 100);
        $total = $subtotal - $discount + $deliveryCharge + $taxAmount + $serviceAmount;

        $deliveryAddress = null;
        if ($orderType === 'delivery') {
            $deliveryAddress = $this->formatDeliveryAddress($validated['address'] ?? []);
        }

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => Auth::id(),
                'order_type' => $orderType,
                'table_number' => $validated['table_number'] ?? null,
                'status' => Order::STATUS_PENDING,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'delivery_charge' => $deliveryCharge,
                'discount_amount' => $discount,
                'total' => $total,
                'total_amount' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === Order::PAYMENT_COD ? 'pending' : 'paid',
                'delivery_address' => $deliveryAddress,
                'customer_name' => $validated['contact']['name'],
                'customer_email' => $validated['contact']['email'] ?? null,
                'customer_phone' => $validated['contact']['phone'],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'promo_code' => $isDirect ? null : Session::get('promo_code'),
                'estimated_preparation_time' => (int) config('restaurant.order.preparation_time_default', 30),
                'estimated_delivery_time' => $orderType === 'delivery'
                    ? now()->addMinutes((int) config('restaurant.order.delivery_time_default', 45))
                    : null,
            ]);

            // Create order items
            foreach ($cart as $key => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['item_id'],
                    'is_custom_meal' => false,
                    'item_name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total'],
                    'size' => $item['size'] ?? null,
                    'flavor' => $item['flavor'] ?? null,
                    'selected_toppings' => $item['toppings'] ?? [],
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);

                // Update item popularity
                MenuItem::where('id', $item['item_id'])->increment('total_orders', $item['quantity']);
            }

            // Create payment record
            $cardDetails = $validated['card'] ?? [];
            $cardLastFour = null;
            $paymentDetails = null;

            if ($paymentMethod === Order::PAYMENT_CARD) {
                $cardNumber = preg_replace('/\D/', '', (string) ($cardDetails['number'] ?? ''));
                $cardLastFour = $cardNumber ? substr($cardNumber, -4) : null;
                $paymentDetails = [
                    'card_holder' => $cardDetails['name'] ?? null,
                    'card_expiry' => $cardDetails['expiry'] ?? null,
                ];
            }

            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $this->generateTransactionId(),
                'payment_method' => $paymentMethod,
                'status' => $paymentMethod === Order::PAYMENT_COD ? Payment::STATUS_PENDING : Payment::STATUS_COMPLETED,
                'amount' => $total,
                'currency' => config('restaurant.payment.currency', 'USD'),
                'payment_details' => $paymentDetails,
                'card_last_four' => $cardLastFour,
                'card_brand' => $paymentMethod === Order::PAYMENT_CARD ? 'card' : null,
                'payment_date' => now(),
                'payer_name' => $validated['contact']['name'],
                'payer_email' => $validated['contact']['email'] ?? null,
                'payer_phone' => $validated['contact']['phone'],
            ]);

            // Use promo code if applied
            if (!$isDirect) {
                if ($promoCode = Session::get('promo_code')) {
                    Promotion::where('promo_code', $promoCode)->increment('times_used');
                    Session::forget(['promo_code', 'discount_amount']);
                }
            }

            // Clear cart or direct checkout
            if ($isDirect) {
                Session::forget('direct_checkout');
            } else {
                Session::forget('cart');
            }

            DB::commit();

            return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed', [
                'message' => $e->getMessage(),
                'order_type' => $validated['delivery_type'] ?? null,
                'user_id' => Auth::id(),
            ]);
            $debugMessage = config('app.debug')
                ? 'Checkout error: ' . $e->getMessage()
                : 'An error occurred while processing your order. Please try again.';

            return back()->with('error', $debugMessage)->withInput();
        }
    }

    /**
     * Display order success page
     */
    public function success(Order $order)
    {
        // Ensure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items', 'payment']);

        return view('customer.order-confirmation', compact('order'));
    }

    /**
     * Apply promo code
     */
    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|max:50',
        ]);

        $promoCode = $request->promo_code;
        $isDirect = $this->isDirectCheckout();

        if ($isDirect) {
            return redirect()->route('checkout')->with('promoError', 'Promo codes cannot be applied to direct checkout orders.');
        }

        $promotion = Promotion::where('promo_code', $promoCode)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promotion) {
            return redirect()->route('checkout')->with('promoError', 'Invalid or expired promo code.');
        }

        $cart = $this->getCheckoutCart();
        $subtotal = $this->getCartTotal($cart);

        // Check minimum order amount
        if ($subtotal < $promotion->minimum_order_amount) {
            return redirect()->route('checkout')->with('promoError', 'Minimum order amount of ' . $promotion->minimum_order_amount . ' required for this promo code.');
        }

        // Calculate discount
        $discount = 0;
        if ($promotion->discount_type === 'percentage') {
            $discount = $subtotal * ($promotion->discount_value / 100);
            if ($promotion->maximum_discount && $discount > $promotion->maximum_discount) {
                $discount = $promotion->maximum_discount;
            }
        } else {
            $discount = $promotion->discount_value;
        }

        // Store promo in session
        Session::put('promo_code', $promoCode);
        Session::put('discount_amount', $discount);
        Session::put('promo_discount_type', $promotion->discount_type);
        Session::put('promo_discount_value', $promotion->discount_value);

        return redirect()->route('checkout')->with('success', 'Promo code applied successfully!');
    }

    /**
     * Validation rules
     */
    private function validationRules()
    {
        return [
            'delivery_type' => 'required|in:delivery,takeaway,dine_in',
            'contact.name' => 'required|string|max:255',
            'contact.email' => 'required|email|max:255',
            'contact.phone' => 'required|string|max:30',
            'address.street' => 'required_if:delivery_type,delivery|nullable|string|max:255',
            'address.apartment' => 'nullable|string|max:255',
            'address.city' => 'required_if:delivery_type,delivery|nullable|string|max:255',
            'address.zip' => 'required_if:delivery_type,delivery|nullable|string|max:30',
            'table_number' => 'nullable|exists:tables,table_number',
            'special_instructions' => 'nullable|string|max:500',
            'payment_method' => 'required|in:card,cash',
            'card.number' => 'required_if:payment_method,card|nullable|string|min:16|max:19',
            'card.expiry' => 'required_if:payment_method,card|nullable|string|min:4|max:7',
            'card.cvv' => 'required_if:payment_method,card|nullable|string|min:3|max:4',
            'card.name' => 'required_if:payment_method,card|nullable|string|max:255',
            'schedule_date' => 'nullable|date|after_or_equal:today',
            'schedule_time' => 'nullable|date_format:H:i',
        ];
    }

    /**
     * Validation messages
     */
    private function messages()
    {
        return [
            'address.street.required_if' => 'Street address is required for delivery orders.',
            'address.city.required_if' => 'City is required for delivery orders.',
            'address.zip.required_if' => 'ZIP code is required for delivery orders.',
            'card.number.required_if' => 'Card number is required for card payments.',
            'card.expiry.required_if' => 'Card expiry is required for card payments.',
            'card.cvv.required_if' => 'Card CVV is required for card payments.',
            'card.name.required_if' => 'Cardholder name is required for card payments.',
        ];
    }

    private function normalizeCheckoutInput(Request $request): array
    {
        $data = $request->all();

        // Backward-compat: allow older payloads to keep working.
        if (!isset($data['delivery_type']) && isset($data['order_type'])) {
            $data['delivery_type'] = $data['order_type'];
        }

        $data['contact'] = is_array($data['contact'] ?? null) ? $data['contact'] : [];
        $data['card'] = is_array($data['card'] ?? null) ? $data['card'] : [];
        $data['address'] = is_array($data['address'] ?? null) ? $data['address'] : [];

        // Contact aliases
        $data['contact']['name'] = $data['contact']['name'] ?? $data['customer_name'] ?? $data['name'] ?? null;
        $data['contact']['email'] = $data['contact']['email'] ?? $data['customer_email'] ?? $data['email'] ?? null;
        $data['contact']['phone'] = $data['contact']['phone'] ?? $data['customer_phone'] ?? $data['phone'] ?? null;

        // Card aliases
        $data['card']['number'] = $data['card']['number'] ?? $data['card_number'] ?? null;
        $data['card']['expiry'] = $data['card']['expiry'] ?? $data['card_expiry'] ?? null;
        $data['card']['cvv'] = $data['card']['cvv'] ?? $data['card_cvc'] ?? null;
        $data['card']['name'] = $data['card']['name'] ?? $data['card_name'] ?? null;

        // Address alias (single text field)
        if (empty($data['address']) && !empty($data['delivery_address'])) {
            $data['address']['street'] = $data['delivery_address'];
        }

        return $data;
    }

    private function formatDeliveryAddress(array $address): string
    {
        $parts = array_filter([
            $address['street'] ?? null,
            $address['apartment'] ?? null,
            $address['city'] ?? null,
            $address['zip'] ?? null,
        ], fn ($value) => is_string($value) ? trim($value) !== '' : $value !== null);

        return implode(', ', $parts);
    }

    /**
     * Get cart from session
     */
    private function getCart()
    {
        return Session::get('cart', []);
    }

    /**
     * Get checkout cart (direct checkout takes precedence)
     */
    private function getCheckoutCart()
    {
        $direct = Session::get('direct_checkout', []);
        return !empty($direct) ? $direct : $this->getCart();
    }

    private function isDirectCheckout(): bool
    {
        $direct = Session::get('direct_checkout', []);
        return !empty($direct);
    }

    /**
     * Get cart total
     */
    private function getCartTotal($cart)
    {
        return array_sum(array_column($cart, 'total'));
    }

    /**
     * Calculate final total
     */
    private function calculateTotal($cart)
    {
        $subtotal = $this->getCartTotal($cart);
        $discount = Session::get('discount_amount', 0);
        $deliveryFee = (float) config('restaurant.order.delivery_charge', 0);
        $freeDeliveryThreshold = (float) config('restaurant.order.free_delivery_threshold', 0);
        $taxRate = (float) config('restaurant.order.tax_rate', 0);
        $serviceChargeRate = (float) config('restaurant.order.service_charge_rate', 0);

        $deliveryCharge = ($freeDeliveryThreshold > 0 && $subtotal >= $freeDeliveryThreshold) ? 0 : $deliveryFee;

        $taxable = max(0, ($subtotal - $discount));
        $taxAmount = $taxable * ($taxRate / 100);
        $serviceAmount = $taxable * ($serviceChargeRate / 100);

        return $subtotal - $discount + $deliveryCharge + $taxAmount + $serviceAmount;
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        return Order::generateUniqueOrderNumber();
    }

    /**
     * Generate transaction ID
     */
    private function generateTransactionId()
    {
        return 'TXN-' . now()->format('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
}
