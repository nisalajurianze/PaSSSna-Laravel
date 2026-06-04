<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoyaltyReward;

class LoyaltyRewardsSeeder extends Seeder
{
    public function run(): void
    {
        LoyaltyReward::create([
            'name' => '10% Off Your Order',
            'description' => 'Get 10% discount on your next order',
            'points_required' => 50,
            'reward_type' => 'discount_percent',
            'reward_value' => 10,
            'minimum_order_amount' => 20,
            'is_active' => true
        ]);

        LoyaltyReward::create([
            'name' => 'Free Appetizer',
            'description' => 'Get a free appetizer (up to $10 value)',
            'points_required' => 100,
            'reward_type' => 'free_item',
            'reward_value' => 10,
            'minimum_order_amount' => 30,
            'is_active' => true
        ]);

        LoyaltyReward::create([
            'name' => '$15 Off',
            'description' => 'Get $15 off on orders over $50',
            'points_required' => 150,
            'reward_type' => 'discount_amount',
            'reward_value' => 15,
            'minimum_order_amount' => 50,
            'is_active' => true
        ]);

        LoyaltyReward::create([
            'name' => 'Free Main Course',
            'description' => 'Get a free main course (up to $25 value)',
            'points_required' => 250,
            'reward_type' => 'free_item',
            'reward_value' => 25,
            'minimum_order_amount' => 50,
            'is_active' => true
        ]);

        LoyaltyReward::create([
            'name' => '25% Off + Free Dessert',
            'description' => 'Get 25% discount and a free dessert',
            'points_required' => 500,
            'reward_type' => 'discount_percent',
            'reward_value' => 25,
            'minimum_order_amount' => 75,
            'is_active' => true
        ]);

        $this->command->info('Loyalty rewards seeded successfully!');
    }
}
