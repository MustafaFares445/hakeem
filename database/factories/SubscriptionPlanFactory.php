<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SubscriptionDurationUnitEnum;
use App\Models\SubscriptionPlan;
use App\Models\TenantType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPlan>
 */
final class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        return [
            'tenant_type_id' => TenantType::factory(),
            'name' => fake()->randomElement(['Monthly Plan', 'Yearly Plan']),
            'description' => fake()->sentence(),
            'original_price' => fake()->randomFloat(2, 100, 500),
            'price' => fake()->randomFloat(2, 80, 450),
            'currency_code' => 'USD',
            'duration_value' => fake()->randomElement([1, 6, 12]),
            'duration_unit' => fake()->randomElement([
                SubscriptionDurationUnitEnum::Month->value,
                SubscriptionDurationUnitEnum::Year->value,
            ]),
            'is_lifetime' => false,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function lifetime(): static
    {
        return $this->state(fn (): array => [
            'name' => 'Lifetime Membership',
            'duration_value' => null,
            'duration_unit' => null,
            'is_lifetime' => true,
        ]);
    }
}
