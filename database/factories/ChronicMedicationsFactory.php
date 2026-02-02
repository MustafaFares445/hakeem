<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChronicMedications>
 */
final class ChronicMedicationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => function (array $attributes) {
                return \App\Models\Patient::factory()->create(['tenant_id' => $attributes['tenant_id'] ?? null])->id;
            },
            'title' => fake()->sentence(),
        ];
    }
}
