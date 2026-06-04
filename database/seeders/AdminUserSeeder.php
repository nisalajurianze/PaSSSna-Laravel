<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User (use firstOrCreate to avoid duplicate entry)
        User::firstOrCreate(
            ['email' => 'admin.passsna@gmail.com'],
            [
                'name' => 'Admin PaSSSna',
                'password' => Hash::make('PaSSSna_log'),
                'role' => 'admin',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Restaurant Street, Food City',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Sample Customers
        $customers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '+1 (555) 111-2222',
                'address' => '123 Main Street, Cityville',
                'payment_card_last_four' => '4242',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '+1 (555) 333-4444',
                'address' => '456 Oak Avenue, Townsville',
                'payment_card_last_four' => '5555',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '+1 (555) 555-6666',
                'address' => '789 Pine Road, Villageton',
                'payment_card_last_four' => '1234',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '+1 (555) 777-8888',
                'address' => '321 Elm Street, Hamlet City',
                'payment_card_last_four' => '9876',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '+1 (555) 999-0000',
                'address' => '654 Maple Drive, Boroughburg',
                'payment_card_last_four' => '4321',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($customers as $customer) {
            User::firstOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }

        // Create 20 more random customers (only if they don't exist)
        User::factory()->count(20)->create([
            'role' => 'customer',
            'is_active' => true,
        ]);
    }
}
