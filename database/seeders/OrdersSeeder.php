<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $menuItems = MenuItem::all();

        $orderTypes = ['dine_in', 'takeaway', 'delivery'];
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'];
        $paymentMethods = ['cash', 'card', 'cash_on_delivery', 'online'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        for ($i = 0; $i < 50; $i++) {
            $customer = $customers->random();
            $orderType = $orderTypes[array_rand($orderTypes)];
            $status = $statuses[array_rand($statuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            // Enforce COD only for delivery
            if ($orderType !== 'delivery' && $paymentMethod === 'cash_on_delivery') {
                $paymentMethod = 'cash';
            }

            $paymentStatus = $paymentMethod === 'cash_on_delivery' ? 'pending' : $paymentStatuses[array_rand($paymentStatuses)];
            if ($status === 'completed') {
                $paymentStatus = 'paid';
            }

            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            
            $order = Order::updateOrCreate(
                ['order_number' => $orderNumber],
                [
                    'order_number' => $orderNumber,
                'user_id' => $customer->id,
                'order_type' => $orderType,
                'table_number' => $orderType === 'dine_in' ? rand(1, 20) : null,
                'status' => $status,
                'subtotal' => 0,
                'tax_amount' => 0,
                'delivery_charge' => $orderType === 'delivery' ? (float) config('restaurant.order.delivery_charge', 0) : 0,
                'discount_amount' => 0,
                'total' => 0,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'delivery_address' => $orderType === 'delivery' ? $customer->address : null,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?: ('+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999)),
                'special_instructions' => rand(0, 1) ? 'Please make it spicy' : null,
                'estimated_delivery_time' => $orderType === 'delivery' ? now()->addMinutes(rand(30, 60)) : null,
                'estimated_preparation_time' => (int) config('restaurant.order.preparation_time_default', 30),
                'created_at' => now()->subDays(rand(0, 60)),
            ]);

            // Add items to order
            $numItems = rand(1, 5);
            $subtotal = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $menuItem = $menuItems->random();
                $quantity = rand(1, 3);
                $price = (float) ($menuItem->offer_price ?? $menuItem->price);
                $itemTotal = $price * $quantity;
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'is_custom_meal' => false,
                    'item_name' => $menuItem->name,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $itemTotal,
                    'size' => rand(0, 1) ? 'medium' : null,
                    'special_instructions' => rand(0, 1) ? 'No onions please' : null,
                ]);
            }

            // Update order totals
            $taxRate = (float) config('restaurant.order.tax_rate', 0);
            $tax = $subtotal * ($taxRate / 100);
            $total = $subtotal + $tax + (float) $order->delivery_charge - (float) $order->discount_amount;

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total' => $total,
            ]);
        }
    }
}
