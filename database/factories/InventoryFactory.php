<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        $currentQty = fake()->randomFloat(3, 1, 50);
        $minQty = fake()->randomFloat(3, 1, min(10, $currentQty));
        $maxQty = max($currentQty + 10, $minQty + 10);
        $unitCost = fake()->randomFloat(2, 0.5, 20);

        return [
            'item_name' => Str::title(fake()->words(2, true)),
            'item_code' => strtoupper(fake()->unique()->bothify('INV-#####')),
            'category' => fake()->randomElement(['vegetable', 'meat', 'dairy', 'spice', 'grain', 'beverage', 'other']),
            'unit' => fake()->randomElement(['kg', 'g', 'l', 'ml', 'piece', 'pack', 'dozen']),
            'current_quantity' => $currentQty,
            'minimum_quantity' => $minQty,
            'maximum_quantity' => $maxQty,
            'unit_cost' => $unitCost,
            'total_value' => $currentQty * $unitCost,
            'status' => $currentQty <= 0 ? 'out_of_stock' : ($currentQty <= $minQty ? 'low_stock' : 'in_stock'),
            'supplier_name' => fake()->company(),
            'supplier_contact' => fake()->phoneNumber(),
            'last_restocked_date' => now()->subDays(fake()->numberBetween(0, 30))->toDateString(),
            'expiry_date' => null,
            'storage_location' => fake()->randomElement(['Pantry', 'Fridge', 'Freezer', 'Dry Storage']),
            'notes' => null,
            'is_active' => true,
            'reorder_quantity' => fake()->randomFloat(3, 5, 50),
            'daily_usage_rate' => fake()->randomFloat(3, 0, 5),
            'days_of_supply' => null,
        ];
    }
}

