<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DentalLab;
use App\Models\FillerMaterial;
use App\Models\MedicalRecord;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalRecordTreatment>
 */
final class MedicalRecordTreatmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medical_record_id' => MedicalRecord::factory(),
            'treatment_id' => Treatment::factory(),
            'treatment_date' => fake()->date(),
            'treatment_cost' => fake()->numberBetween(100, 5000),
            'treatment_description' => fake()->paragraph(),
            'tooth_position' => fake()->randomElement(['11', '12', '13', '14', '15', '16', '17', '18', '21', '22']),
            'filler_material_id' => FillerMaterial::factory(),
            'dental_lab_id' => DentalLab::factory(),
            'session_number' => fake()->numberBetween(1, 5),
        ];
    }
}
