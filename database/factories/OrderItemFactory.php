<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 4);
        $unitPrice = fake()->randomFloat(2, 5, 50);

        return [
            'order_id' => Order::factory(),
            'menu_item_id' => MenuItem::factory(),
            'is_custom_meal' => false,
            'item_name' => fake()->words(2, true),
            'description' => null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
            'size' => null,
            'flavor' => null,
            'selected_toppings' => [],
            'custom_ingredients' => null,
            'special_instructions' => null,
            'is_prepared' => false,
            'prepared_at' => null,
            'prepared_by' => null,
        ];
    }
}

