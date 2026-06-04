<?php

namespace Database\Factories;

use App\Models\DiningSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DiningSession>
 */
class DiningSessionFactory extends Factory
{
    protected $model = DiningSession::class;

    public function definition(): array
    {
        $start = now()->subMinutes(fake()->numberBetween(0, 180));

        return [
            'session_code' => 'DS-' . strtoupper(Str::random(8)),
            'user_id' => User::factory(),
            'table_number' => fake()->numberBetween(1, 20),
            'number_of_people' => fake()->numberBetween(1, 8),
            'status' => DiningSession::STATUS_ACTIVE,
            'start_time' => $start,
            'end_time' => null,
            'total_bill' => 0,
            'amount_paid' => 0,
            'remaining_balance' => 0,
            'payment_completed' => false,
            'notes' => null,
            'assigned_waiter' => null,
            'custom_meal_preferences' => null,
            'exit_password' => null,
            'exit_with_admin_password' => false,
            'last_order_time' => null,
        ];
    }
}

