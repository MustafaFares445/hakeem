<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AppointmentTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
final class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => null,
            'tenant_id' => null,
            'user_id' => null,
            'date' => fake()->date(),
            'time' => fake()->word(),
            'appointment_type' => fake()->randomElement(AppointmentTypeEnum::cases())->value,
        ];
    }
}
