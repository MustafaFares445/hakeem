<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionOrder>
 */
final class SubscriptionOrderFactory extends Factory
{
    protected $model = SubscriptionOrder::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'subscription_plan_id' => SubscriptionPlan::factory(),
            'created_by_user_id' => User::factory(),
            'status' => SubscriptionOrderStatusEnum::Pending->value,
            'plan_name' => 'Monthly Plan',
            'plan_description' => fake()->sentence(),
            'original_price' => 29.00,
            'price' => 23.20,
            'currency_code' => 'USD',
            'duration_value' => 1,
            'duration_unit' => 'month',
            'is_lifetime' => false,
            'starts_at' => null,
            'ends_at' => null,
            'confirmed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }
}
