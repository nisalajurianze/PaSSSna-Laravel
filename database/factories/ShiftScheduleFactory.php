<?php

namespace Database\Factories;

use App\Models\ShiftSchedule;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShiftSchedule>
 */
class ShiftScheduleFactory extends Factory
{
    protected $model = ShiftSchedule::class;

    public function definition(): array
    {
        $shiftDate = now()->addDays(fake()->numberBetween(0, 14))->toDateString();
        $start = fake()->randomElement(['09:00', '11:00', '14:00', '17:00']);

        $end = match ($start) {
            '09:00' => '17:00',
            '11:00' => '19:00',
            '14:00' => '22:00',
            default => '01:00',
        };

        return [
            'staff_id' => Staff::factory(),
            'shift_date' => $shiftDate,
            'start_time' => $start,
            'end_time' => $end,
            'shift_type' => fake()->randomElement(['morning', 'afternoon', 'evening', 'night', 'split']),
            'status' => fake()->randomElement(['scheduled', 'ongoing', 'completed', 'cancelled', 'absent']),
            'location' => null,
            'assigned_tables' => null,
            'assigned_duties' => null,
            'notes' => null,
            'hours_worked' => null,
            'clock_in_time' => null,
            'clock_out_time' => null,
            'overtime_hours' => 0,
            'is_approved' => false,
            'approved_by' => null,
        ];
    }
}

