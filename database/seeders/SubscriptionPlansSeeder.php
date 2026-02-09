<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SubscriptionDurationUnitEnum;
use App\Models\SubscriptionPlan;
use App\Models\TenantType;
use Illuminate\Database\Seeder;

final class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $tenantType = TenantType::query()->firstOrCreate(
            ['key' => 'small_clinic'],
            [
                'name' => 'Small Clinic',
                'description' => 'Default clinic tenant type.',
                'is_active' => true,
            ]
        );

        $plans = [
            [
                'name' => 'Monthly Plan',
                'description' => 'Billed monthly',
                'original_price' => 29.00,
                'price' => 23.20,
                'currency_code' => 'USD',
                'duration_value' => 1,
                'duration_unit' => SubscriptionDurationUnitEnum::Month->value,
                'is_lifetime' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Yearly Plan',
                'description' => 'Billed yearly',
                'original_price' => 129.00,
                'price' => 103.20,
                'currency_code' => 'USD',
                'duration_value' => 1,
                'duration_unit' => SubscriptionDurationUnitEnum::Year->value,
                'is_lifetime' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Lifetime Membership',
                'description' => 'One-time lifetime access',
                'original_price' => 399.00,
                'price' => 319.20,
                'currency_code' => 'USD',
                'duration_value' => null,
                'duration_unit' => null,
                'is_lifetime' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::query()->updateOrCreate(
                [
                    'tenant_type_id' => $tenantType->id,
                    'name' => $plan['name'],
                ],
                $plan
            );
        }
    }
}
