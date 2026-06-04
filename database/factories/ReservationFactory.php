<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $date = Carbon::today()->addDays(1);
        $time = fake()->randomElement(['18:00', '19:00', '20:00']);
        $duration = 90;

        $startAt = Carbon::parse($date->toDateString() . ' ' . $time);
        $endAt = $startAt->copy()->addMinutes($duration);

        return [
            'reservation_number' => 'RSV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
            'user_id' => null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'reservation_date' => $date->toDateString(),
            'reservation_time' => $time,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'duration_minutes' => $duration,
            'number_of_people' => fake()->numberBetween(1, 8),
            'table_numbers' => [],
            'table_count' => 0,
            'status' => Reservation::STATUS_PENDING,
            'reservation_type' => 'regular',
            'special_requests' => null,
            'occasion_notes' => null,
            'confirmed_by' => null,
            'confirmed_at' => null,
            'confirmation_message' => null,
            'arrival_time' => null,
            'seated_time' => null,
            'departure_time' => null,
            'deposit_amount' => 0,
            'deposit_paid' => false,
            'cancellation_reason' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Reservation $reservation) {
            if ($reservation->tables()->exists()) {
                return;
            }

            $table = Table::factory()->create();

            $reservation->tables()->attach($table->id);
            $reservation->update([
                'table_numbers' => [$table->table_number],
                'table_count' => 1,
            ]);
        });
    }
}

