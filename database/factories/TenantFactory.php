<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TenantTypes;
use App\Models\Tenant;
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

        DB::table('tenants')->insert([
            'id' => $tenantId,
            'name' => $attributes['name'] ?? fake()->company(),
            'type' => $attributes['type'] ?? TenantTypes::SMALL_CLINIC->value,
            'domain_name' => $attributes['domain_name'] ?? fake()->unique()->slug(),
            'data' => json_encode($attributes['data'] ?? [], JSON_THROW_ON_ERROR),
            'tenant_id' => $parent?->id,
            'created_at' => now(),
            'updated_at' => now(),
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
