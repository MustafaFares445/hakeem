<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PatientGenderEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
final class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'birthday' => fake()->date(),
            'gender' => fake()->randomElement(array_map(fn ($case) => $case->value, PatientGenderEnum::cases())),
            'city' => fake()->city(),
            'street_address' => fake()->address(),
            'registration_date' => fake()->date(),
            'notes' => fake()->paragraph(),
        ];
    }
}
