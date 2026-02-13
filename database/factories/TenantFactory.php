<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonException;

/**
 * @extends Factory<Tenant>
 */
final class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
        ];
    }

    /**
     * Create a new model instance.
     *
     * @throws JsonException
     */
    public function create($attributes = [], ?\Illuminate\Database\Eloquent\Model $parent = null): Tenant
    {
        $tenantId = $attributes['id'] ?? Str::uuid()->toString();
        $tenantType = TenantType::query()->firstOrCreate(
            ['key' => 'small_clinic'],
            [
                'name' => 'Small Clinic',
                'description' => null,
                'is_active' => true,
            ]
        );

        $createdAt = $attributes['created_at'] ?? now();
        $trialStartsAt = $attributes['trial_starts_at'] ?? $createdAt;
        $trialStartsAtCarbon = $trialStartsAt instanceof Carbon ? $trialStartsAt : Carbon::parse((string) $trialStartsAt);
        $trialEndsAt = $attributes['trial_ends_at'] ?? $trialStartsAtCarbon->copy()->addMonth();

        DB::table('tenants')->insert([
            'id' => $tenantId,
            'name' => $attributes['name'] ?? fake()->company(),
            'tenant_type_id' => $attributes['tenant_type_id'] ?? $tenantType->id,
            'domain_name' => $attributes['domain_name'] ?? fake()->unique()->slug(),
            'data' => json_encode($attributes['data'] ?? [], JSON_THROW_ON_ERROR),
            'tenant_id' => $parent?->id,
            'trial_starts_at' => $trialStartsAt,
            'trial_ends_at' => $trialEndsAt,
            'is_suspended' => $attributes['is_suspended'] ?? false,
            'suspended_at' => $attributes['suspended_at'] ?? null,
            'suspension_reason' => $attributes['suspension_reason'] ?? null,
            'suspended_by_user_id' => $attributes['suspended_by_user_id'] ?? null,
            'created_at' => $createdAt,
            'updated_at' => $attributes['updated_at'] ?? now(),
        ]);

        return Tenant::find($tenantId);
    }

    /**
     * Indicate that the tenant should have Arabic data.
     */
    public function arabic(): static
    {
        $arabicClinicNames = [
            'عيادة الحكيم الطبية',
            'عيادة النور للرعاية الصحية',
            'عيادة الشفاء الطبية',
            'عيادة الأمل الصحية',
            'عيادة الرعاية المتكاملة',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($arabicClinicNames),
            'domain_name' => fake()->unique()->slug(),
            'data' => json_encode([
                'description' => fake()->randomElement([
                    'عيادة طبية متخصصة في الرعاية الصحية الشاملة',
                    'مركز طبي يقدم خدمات صحية متكاملة',
                    'عيادة متخصصة في الطب العام والرعاية الأولية',
                ]),
                'address' => fake()->randomElement([
                    'الرياض، المملكة العربية السعودية',
                    'جدة، المملكة العربية السعودية',
                    'الدمام، المملكة العربية السعودية',
                ]),
            ], JSON_THROW_ON_ERROR),
        ]);
    }
}
