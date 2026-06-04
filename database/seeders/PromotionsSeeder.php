<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionsSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            [
                'promo_code' => 'WELCOME20',
                'name' => 'Welcome Offer',
                'description' => 'Welcome offer for new customers',
                'type' => 'percentage',
                'discount_value' => 20,
                'minimum_order_amount' => 25.00,
                'maximum_uses' => 100,
                'times_used' => 45,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'is_active' => true,
                'applicable_categories' => json_encode(['all']),
            ],
            [
                'promo_code' => 'FREESHIP',
                'name' => 'Free Delivery',
                'description' => 'Free delivery on orders above $40',
                'type' => 'fixed',
                'discount_value' => 5.99,
                'minimum_order_amount' => 40.00,
                'maximum_uses' => 200,
                'times_used' => 89,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(30),
                'is_active' => true,
                'applicable_categories' => json_encode(['delivery']),
            ],
            [
                'promo_code' => 'WEEKEND25',
                'name' => 'Weekend Special',
                'description' => 'Weekend special - 25% off on all pizzas',
                'type' => 'percentage',
                'discount_value' => 25,
                'minimum_order_amount' => 30.00,
                'maximum_uses' => 50,
                'times_used' => 22,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(2),
                'is_active' => true,
                'applicable_categories' => json_encode(['main_course']),
            ],
            [
                'promo_code' => 'HAPPYHOUR',
                'name' => 'Happy Hour',
                'description' => 'Happy hour drinks discount',
                'type' => 'percentage',
                'discount_value' => 15,
                'minimum_order_amount' => 10.00,
                'maximum_uses' => 100,
                'times_used' => 35,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(30),
                'is_active' => true,
                'applicable_categories' => json_encode(['beverage']),
            ],
            [
                'promo_code' => 'FAMILYMEAL',
                'name' => 'Family Meal Bundle',
                'description' => 'Family meal bundle discount',
                'type' => 'fixed',
                'discount_value' => 12.00,
                'minimum_order_amount' => 60.00,
                'maximum_uses' => 150,
                'times_used' => 67,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addDays(60),
                'is_active' => true,
                'applicable_categories' => json_encode(['special']),
            ],
        ];

        foreach ($promotions as $promotion) {
            Promotion::firstOrCreate(
                ['promo_code' => $promotion['promo_code']],
                $promotion
            );
        }
    }
}
