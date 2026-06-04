<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'employee_id' => strtoupper(fake()->unique()->bothify('EMP-####')),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'password' => 'password',
            'photo' => null,
            'role' => fake()->randomElement([
                Staff::ROLE_CHEF,
                Staff::ROLE_WAITER,
                Staff::ROLE_MANAGER,
                Staff::ROLE_BARTENDER,
                Staff::ROLE_HOST,
                Staff::ROLE_CASHIER,
                Staff::ROLE_DELIVERY_BOY,
            ]),
            'status' => Staff::STATUS_ACTIVE,
            'salary' => fake()->randomFloat(2, 0, 5000),
            'hire_date' => now()->subDays(fake()->numberBetween(0, 365))->toDateString(),
            'termination_date' => null,
            'address' => fake()->address(),
            'emergency_contact' => fake()->name(),
            'emergency_phone' => fake()->phoneNumber(),
            'notes' => null,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }
}

