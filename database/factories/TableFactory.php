<?php

namespace Database\Factories;

use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        $capacity = fake()->numberBetween(2, 8);

        return [
            'table_number' => (string) fake()->unique()->numberBetween(1, 200),
            'name' => null,
            'type' => fake()->randomElement(['indoor', 'outdoor', 'private_room', 'bar']),
            'capacity' => $capacity,
            'min_capacity' => null,
            'location' => null,
            'area' => null,
            'status' => 'available',
            'is_active' => true,
            'features' => null,
            'reservation_fee' => null,
            'sort_order' => 0,
        ];
    }
}

