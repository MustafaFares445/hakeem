<?php

declare(strict_types=1);

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class FillerMaterialPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'filler-materials.viewAny',
            'filler-materials.view',
            'filler-materials.create',
            'filler-materials.update',
            'filler-materials.delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
    }
}
