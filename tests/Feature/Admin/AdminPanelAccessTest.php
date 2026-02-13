<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Models\Tenant;
use App\Models\User;

beforeEach(function (): void {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
});

it('blocks tenant users from accessing the admin panel', function (): void {
    $tenant = Tenant::factory()->create();

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
    ]);
    $user->assignRole(RoleEnum::SystemAdmin->value);

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

it('blocks central users without system admin role from accessing the admin panel', function (): void {
    $user = User::factory()->create([
        'tenant_id' => null,
    ]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

it('allows system admins to access the admin panel', function (): void {
    $user = User::factory()->create([
        'tenant_id' => null,
    ]);
    $user->assignRole(RoleEnum::SystemAdmin->value);

    $this->actingAs($user)
        ->get('/admin')
        ->assertOk();
});

it('allows system admins to access admin resources', function (): void {
    $user = User::factory()->create([
        'tenant_id' => null,
    ]);
    $user->assignRole(RoleEnum::SystemAdmin->value);

    foreach ([
        '/admin/tenants',
        '/admin/subscription-plans',
        '/admin/subscription-orders',
        '/admin/users',
        '/admin/admin-action-logs',
        '/admin/health',
    ] as $uri) {
        $this->actingAs($user)
            ->get($uri)
            ->assertOk();
    }
});
