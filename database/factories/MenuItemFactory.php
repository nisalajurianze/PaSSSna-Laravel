<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $basePrice = fake()->randomFloat(2, 5, 50);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'base_price' => $basePrice,
            'sizes' => [
                'regular' => $basePrice,
                'medium' => $basePrice + 2,
                'large' => $basePrice + 4,
            ],
            'flavors' => [
                'classic' => 0,
                'spicy' => 1,
                'garlic' => 1.5,
            ],
            'extra_toppings' => [
                'extra_cheese' => 1.5,
                'mushrooms' => 1.0,
            ],
            'category' => fake()->randomElement(['appetizer', 'main_course', 'dessert', 'beverage', 'special', 'custom']),
            'food_type' => fake()->randomElement(['vegetarian', 'non_vegetarian', 'vegan']),
            'preparation_time' => fake()->numberBetween(10, 40),
            'is_available' => true,
            'is_fast_moving' => fake()->boolean(20),
            'is_recommended' => fake()->boolean(15),
            'is_customizable' => fake()->boolean(30),
            'ingredients' => [fake()->word(), fake()->word(), fake()->word()],
            'nutrition_info' => [
                'calories' => fake()->numberBetween(150, 900),
            ],
            'offer_price' => null,
            'offer_valid_from' => null,
            'offer_valid_to' => null,
            'offer_valid_until' => null,
            'min_order_qty' => 1,
            'max_order_qty' => 10,
            'image' => null,
            'sort_order' => 0,
            'total_orders' => 0,
            'average_rating' => 0,
            'rating_count' => 0,
        ];
    }

    public function withOffer(): static
    {
        return $this->state(function (array $attributes) {
            $price = (float) ($attributes['base_price'] ?? 10);
            return [
                'offer_price' => max(1, $price - fake()->randomFloat(2, 1, min(5, $price - 1))),
                'offer_valid_until' => now()->addDays(7),
            ];
        });
    }
}

