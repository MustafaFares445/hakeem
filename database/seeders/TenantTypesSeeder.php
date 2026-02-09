<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TenantType;
use Illuminate\Database\Seeder;

final class TenantTypesSeeder extends Seeder
{
    public function run(): void
    {
        TenantType::query()->updateOrCreate(
            ['key' => 'small_clinic'],
            [
                'name' => 'Small Clinic',
                'description' => 'Default clinic tenant type.',
                'is_active' => true,
            ]
        );
    }
}
