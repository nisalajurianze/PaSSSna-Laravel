<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed']);
        $discountValue = $type === 'percentage'
            ? fake()->numberBetween(5, 30)
            : fake()->randomFloat(2, 1, 20);

        return [
            'promo_code' => strtoupper(Str::random(8)),
            'name' => Str::title(fake()->words(2, true)),
            'description' => fake()->sentence(),
            'type' => $type,
            'discount_value' => $discountValue,
            'minimum_order_amount' => fake()->randomFloat(2, 0, 50),
            'maximum_uses' => null,
            'uses_per_customer' => 1,
            'times_used' => 0,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'is_active' => true,
            'is_visible' => true,
            'applicable_categories' => ['all'],
            'excluded_items' => [],
            'free_item_id' => null,
            'free_item_quantity' => 1,
        ];
    }
}

