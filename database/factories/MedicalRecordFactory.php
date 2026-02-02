<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RecordTypeEnum;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalRecord>
 */
final class MedicalRecordFactory extends Factory
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
                return Patient::factory()->create(['tenant_id' => $attributes['tenant_id'] ?? null])->id;
            },
            'record_date' => fake()->date(),
            'record_type' => fake()->randomElement(array_map(fn ($case) => $case->value, RecordTypeEnum::cases())),
            'case_name' => fake()->words(3, asText: true),
            'description' => fake()->paragraph(),
        ];
    }
}
