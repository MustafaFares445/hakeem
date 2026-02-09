<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TenantType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TenantType>
 */
final class TenantTypeFactory extends Factory
{
    protected $model = TenantType::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $key = Str::of($name)->lower()->replace(' ', '_')->toString();

        return [
            'key' => $key,
            'name' => Str::headline($key),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
