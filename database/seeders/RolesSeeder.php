<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as SpatieRole;

final class RolesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleEnum::cases() as $role) {
            SpatieRole::firstOrCreate(
                ['name' => $role->value, 'guard_name' => 'web']
            );
        }
    }
}
