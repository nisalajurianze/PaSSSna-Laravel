<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $orderType = fake()->randomElement([Order::TYPE_DINE_IN, Order::TYPE_TAKEAWAY, Order::TYPE_DELIVERY]);
        $subtotal = fake()->randomFloat(2, 10, 150);
        $taxRate = (float) config('restaurant.order.tax_rate', 0);
        $taxAmount = $subtotal * ($taxRate / 100);
        $deliveryCharge = $orderType === Order::TYPE_DELIVERY
            ? (float) config('restaurant.order.delivery_charge', 0)
            : 0.0;
        $discountAmount = 0.0;
        $total = $subtotal + $taxAmount + $deliveryCharge - $discountAmount;

        return [
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . fake()->unique()->numberBetween(1000, 9999),
            'user_id' => User::factory(),
            'order_type' => $orderType,
            'table_number' => $orderType === Order::TYPE_DINE_IN ? fake()->numberBetween(1, 30) : null,
            'status' => Order::STATUS_PENDING,
            'payment_status' => 'paid',
            'payment_method' => Order::PAYMENT_CASH,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'delivery_charge' => $deliveryCharge,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'promo_code' => null,
            'delivery_address' => $orderType === Order::TYPE_DELIVERY ? fake()->address() : null,
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_email' => fake()->safeEmail(),
            'special_instructions' => null,
            'estimated_preparation_time' => (int) config('restaurant.order.preparation_time_default', 30),
            'estimated_delivery_time' => $orderType === Order::TYPE_DELIVERY ? now()->addMinutes((int) config('restaurant.order.delivery_time_default', 45)) : null,
        ];
    }
}

