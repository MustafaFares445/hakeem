<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Billing>
 */
final class BillingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'type' => BillingTypeEnum::Incoming,
            'date' => fake()->date(),
            'patient_id' => null,
            'user_id' => null,
            'medical_record_id' => null,
            'case_name' => fake()->words(3, true),
            'paid_amount' => fake()->randomFloat(2, 0, 500),
            'total_cost' => fake()->randomFloat(2, 0, 1000),
            'item_name' => null,
            'quantity' => null,
            'amount' => null,
            'outgoing_type' => null,
        ];
    }

    public function incoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BillingTypeEnum::Incoming,
            'item_name' => null,
            'quantity' => null,
            'amount' => null,
            'outgoing_type' => null,
        ]);
    }

    public function outgoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => BillingTypeEnum::Outgoing,
            'patient_id' => null,
            'user_id' => null,
            'medical_record_id' => null,
            'case_name' => null,
            'paid_amount' => null,
            'total_cost' => null,
            'item_name' => fake()->words(2, true),
            'quantity' => fake()->numberBetween(1, 10),
            'amount' => fake()->randomFloat(2, 0, 500),
            'outgoing_type' => fake()->randomElement(BillingOutgoingTypeEnum::cases())->value,
        ]);
    }
}
