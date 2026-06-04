<?php

namespace Database\Seeders;

use App\Models\CustomIngredient;
use Illuminate\Database\Seeder;

class CustomIngredientsSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Base Options
            [
                'name' => 'White Rice',
                'category' => 'base',
                'price' => 2.50,
                'is_available' => true,
                'description' => 'Steamed long grain white rice',
                'calories' => 130,
                'food_type' => 'vegetarian',
            ],
            [
                'name' => 'Brown Rice',
                'category' => 'base',
                'price' => 3.00,
                'is_available' => true,
                'description' => 'Healthy whole grain brown rice',
                'calories' => 112,
                'food_type' => 'vegetarian',
            ],
            [
                'name' => 'Jasmine Rice',
                'category' => 'base',
                'price' => 3.25,
                'is_available' => true,
                'description' => 'Fragrant jasmine rice',
                'calories' => 150,
                'food_type' => 'vegetarian',
            ],
            [
                'name' => 'Egg Noodles',
                'category' => 'base',
                'price' => 3.50,
                'is_available' => true,
                'description' => 'Traditional egg noodles',
                'calories' => 140,
                'food_type' => 'vegetarian',
                'is_spicy' => false,
            ],

            // Protein Options
            [
                'name' => 'Grilled Chicken',
                'category' => 'protein',
                'price' => 6.50,
                'is_available' => true,
                'description' => 'Tender grilled chicken breast',
                'calories' => 165,
                'food_type' => 'non_vegetarian',
            ],
            [
                'name' => 'Beef Strips',
                'category' => 'protein',
                'price' => 8.50,
                'is_available' => true,
                'description' => 'Thinly sliced beef strips',
                'calories' => 210,
                'food_type' => 'non_vegetarian',
            ],
            [
                'name' => 'Shrimp',
                'category' => 'protein',
                'price' => 9.50,
                'is_available' => true,
                'description' => 'Fresh shrimp',
                'calories' => 85,
                'food_type' => 'non_vegetarian',
            ],
            [
                'name' => 'Tofu',
                'category' => 'protein',
                'price' => 5.50,
                'is_available' => true,
                'description' => 'Firm tofu cubes',
                'calories' => 94,
                'food_type' => 'vegan',
            ],

            // Vegetable Options
            [
                'name' => 'Broccoli',
                'category' => 'vegetable',
                'price' => 2.00,
                'is_available' => true,
                'description' => 'Steamed broccoli florets',
                'calories' => 31,
                'food_type' => 'vegan',
            ],
            [
                'name' => 'Carrots',
                'category' => 'vegetable',
                'price' => 1.75,
                'is_available' => true,
                'description' => 'Sliced carrots',
                'calories' => 25,
                'food_type' => 'vegan',
            ],
            [
                'name' => 'Bell Peppers',
                'category' => 'vegetable',
                'price' => 2.25,
                'is_available' => true,
                'description' => 'Mixed bell peppers',
                'calories' => 20,
                'food_type' => 'vegan',
            ],
            [
                'name' => 'Mushrooms',
                'category' => 'vegetable',
                'price' => 2.50,
                'is_available' => true,
                'description' => 'Sautéed mushrooms',
                'calories' => 15,
                'food_type' => 'vegan',
            ],

            // Sauce Options
            [
                'name' => 'Teriyaki Sauce',
                'category' => 'sauce',
                'price' => 1.50,
                'is_available' => true,
                'description' => 'Sweet and savory teriyaki sauce',
                'calories' => 60,
                'food_type' => 'vegan',
            ],
            [
                'name' => 'Spicy Garlic Sauce',
                'category' => 'sauce',
                'price' => 1.75,
                'is_available' => true,
                'description' => 'Garlic sauce with chili',
                'calories' => 45,
                'food_type' => 'vegan',
                'is_spicy' => true,
            ],
            [
                'name' => 'Sweet Chili Sauce',
                'category' => 'sauce',
                'price' => 1.50,
                'is_available' => true,
                'description' => 'Sweet and spicy chili sauce',
                'calories' => 50,
                'food_type' => 'vegan',
            ],
            [
                'name' => 'Soy Sauce',
                'category' => 'sauce',
                'price' => 1.00,
                'is_available' => true,
                'description' => 'Traditional soy sauce',
                'calories' => 10,
                'food_type' => 'vegan',
            ],

            // Toppings
            [
                'name' => 'Fried Egg',
                'category' => 'topping',
                'price' => 2.00,
                'is_available' => true,
                'description' => 'Sunny side up egg',
                'calories' => 90,
                'food_type' => 'vegetarian',
            ],
            [
                'name' => 'Spring Onions',
                'category' => 'topping',
                'price' => 0.75,
                'is_available' => true,
                'description' => 'Chopped spring onions',
                'calories' => 5,
                'food_type' => 'vegan',
            ],
            [
                'name' => 'Sesame Seeds',
                'category' => 'topping',
                'price' => 0.50,
                'is_available' => true,
                'description' => 'Toasted sesame seeds',
                'calories' => 15,
                'food_type' => 'vegan',
            ],
            [
                'name' => 'Crushed Peanuts',
                'category' => 'topping',
                'price' => 1.00,
                'is_available' => true,
                'description' => 'Crushed roasted peanuts',
                'calories' => 45,
                'food_type' => 'vegan',
                'is_nut_free' => false,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            CustomIngredient::create($ingredient);
        }
    }
}
