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
        $group = 'subscription_plans';
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($actions as $action) {
            Permission::firstOrCreate([
                'name' => PermissionNameResolver::resolve($group, $action),
            ]);
        }
    }
}
