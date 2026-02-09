<?php

declare(strict_types=1);

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Mrmarchone\LaravelAutoCrud\Helpers\PermissionNameResolver;
use Spatie\Permission\Models\Permission;

final class SubscriptionPlanPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate([
            'name' => PermissionNameResolver::resolve('subscription_plans', 'view'),
        ]);
    }
}
