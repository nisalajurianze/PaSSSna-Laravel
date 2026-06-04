<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Promotion;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function calculateTotal(float $subtotal, float $taxRatePercent, float $deliveryCharge): float
    {
        return $subtotal + ($subtotal * ($taxRatePercent / 100)) + $deliveryCharge;
    }

    public function createOrder(array $data, array $cartItems)
    {
        return DB::transaction(function () use ($data, $cartItems) {
            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Calculate totals
            $totals = $this->calculateOrderTotals($cartItems, $data['promo_code'] ?? null);

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id() ?? null,
                'order_type' => $data['order_type'],
                'table_number' => $data['table_number'] ?? null,
                'status' => Order::STATUS_PENDING,
                'subtotal' => $totals['subtotal'],
                'tax_rate' => config('app.tax_rate', 5), // Default tax rate
                'tax_amount' => $totals['tax_amount'],
                'delivery_charge' => $totals['delivery_charge'],
                'discount_amount' => $totals['discount_amount'],
                'total' => $totals['total'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'delivery_address' => $data['delivery_address'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'special_instructions' => $data['special_instructions'] ?? null,
                'promo_code' => $data['promo_code'] ?? null,
                'estimated_delivery_time' => $this->calculateEstimatedDeliveryTime($data['order_type']),
                'is_dining' => $data['is_dining'] ?? false,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'size' => $item['size'] ?? null,
                    'flavor' => $item['flavor'] ?? null,
                    'extra_toppings' => $item['extra_toppings'] ?? null,
                    'custom_ingredients' => $item['custom_ingredients'] ?? null,
                    'special_instructions' => $item['special_instructions'] ?? null,
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // Create payment record
            if ($data['payment_method'] !== Order::PAYMENT_COD) {
                Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => 'TXN-' . $orderNumber,
                    'payment_method' => $data['payment_method'],
                    'amount' => $order->total,
                    'status' => Payment::STATUS_PENDING,
                    'payment_details' => $data['payment_details'] ?? null,
                ]);
            }

            // Update promotion usage
            if (!empty($data['promo_code'])) {
                $promotion = Promotion::where('code', $data['promo_code'])->first();
                if ($promotion) {
                    $promotion->increment('times_used');
                }
            }

            return $order;
        });
    }

    public function updateOrderStatus(Order $order, string $status, ?string $notes = null)
    {
        $order->status = $status;
        if ($notes) {
            $order->notes = $notes;
        }

        // Update timestamps based on status
        switch ($status) {
            case Order::STATUS_PREPARING:
                $order->preparation_time = now();
                break;

            case Order::STATUS_READY:
                $order->actual_delivery_time = now();
                break;

            case Order::STATUS_COMPLETED:
                $order->completed_at = now();
                break;
        }

        $order->save();

        // TODO: Trigger event for status update notification

        return $order;
    }

    public function cancelOrder(Order $order, string $reason)
    {
        if (!$order->canBeCancelled()) {
            throw new \Exception('Order cannot be cancelled at this stage.');
        }

        $order->status = Order::STATUS_CANCELLED;
        $order->cancellation_reason = $reason;
        $order->save();

        // TODO: Process refund if payment was made
        // TODO: Trigger cancellation event

        return $order;
    }

    private function generateOrderNumber()
    {
        return Order::generateUniqueOrderNumber();
    }

    private function calculateOrderTotals(array $cartItems, $promoCode = null)
    {
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $taxRate = config('app.tax_rate', 5);
        $taxAmount = $subtotal * ($taxRate / 100);

        $deliveryCharge = config('app.delivery_charge', 50);

        $discountAmount = 0;
        if ($promoCode) {
            $promotion = Promotion::where('code', $promoCode)
                ->where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where('valid_until', '>=', now())
                ->first();

            if ($promotion) {
                $discountAmount = $promotion->calculateDiscount($subtotal);
            }
        }

        $total = $subtotal + $taxAmount + $deliveryCharge - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'delivery_charge' => $deliveryCharge,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }

    private function calculateEstimatedDeliveryTime($orderType)
    {
        $baseTime = now();

        switch ($orderType) {
            case Order::TYPE_DINE_IN:
                $baseTime->addMinutes(20); // 20 minutes preparation
                break;

            case Order::TYPE_TAKEAWAY:
                $baseTime->addMinutes(25); // 25 minutes preparation
                break;

            case Order::TYPE_DELIVERY:
                $baseTime->addMinutes(40); // 40 minutes total
                break;
        }

        return $baseTime;
    }

    public function getOrderStatistics($period = 'today')
    {
        $query = Order::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;

            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;

            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;

            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        return [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'average_order_value' => $query->avg('total') ?? 0,
            'completed_orders' => $query->where('status', Order::STATUS_COMPLETED)->count(),
            'pending_orders' => $query->where('status', Order::STATUS_PENDING)->count(),
        ];
    }

    public function getOrdersByStatus($status)
    {
        return Order::with(['user', 'items'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrderTimeline(Order $order)
    {
        $timeline = [];

        $timeline[] = [
            'time' => $order->created_at,
            'event' => 'Order Placed',
            'description' => 'Order #' . $order->order_number . ' was placed',
            'icon' => 'fas fa-shopping-cart'
        ];

        if ($order->confirmed_at) {
            $timeline[] = [
                'time' => $order->confirmed_at,
                'event' => 'Order Confirmed',
                'description' => 'Order was confirmed by restaurant',
                'icon' => 'fas fa-check-circle'
            ];
        }

        if ($order->preparation_time) {
            $timeline[] = [
                'time' => $order->preparation_time,
                'event' => 'Preparation Started',
                'description' => 'Chef started preparing your order',
                'icon' => 'fas fa-utensils'
            ];
        }

        if ($order->actual_delivery_time) {
            $timeline[] = [
                'time' => $order->actual_delivery_time,
                'event' => 'Order Ready',
                'description' => 'Your order is ready',
                'icon' => 'fas fa-check'
            ];
        }

        if ($order->completed_at) {
            $timeline[] = [
                'time' => $order->completed_at,
                'event' => 'Order Completed',
                'description' => 'Order was completed successfully',
                'icon' => 'fas fa-flag-checkered'
            ];
        }

        usort($timeline, function ($a, $b) {
            return $a['time'] <=> $b['time'];
        });

        return $timeline;
    }
}
