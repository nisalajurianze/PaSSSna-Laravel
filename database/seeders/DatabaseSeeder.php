<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            TablesSeeder::class,
            MenuItemsSeeder::class,
            StaffSeeder::class,
            InventorySeeder::class,
            CustomIngredientsSeeder::class,
            PromotionsSeeder::class,
            ReviewsSeeder::class,
            ContactMessagesSeeder::class,
            OrdersSeeder::class,
            // Add more seeders as needed
        ]);
    }
}
