<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(function (): void {
        Str::createRandomStringsNormally();
        Str::createUuidsNormally();
        Http::preventStrayRequests();
        Process::preventStrayProcesses();
        Sleep::fake();

        $this->freezeTime();

        // Configure tenancy to use the same database in tests
        // Disable database tenancy bootstrapper for tests - use same database
        config([
            'tenancy.bootstrappers' => array_filter(
                config('tenancy.bootstrappers'),
                fn ($bootstrapper) => $bootstrapper !== Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class
            ),
        ]);
    })
    ->in('Feature', 'Unit');

expect()->extend('toBeOne', fn () => $this->toBe(1));

function grantUserPermissions(App\Models\User $user): void
{
    grantPermissions($user, 'users');
}

function grantPermissions(App\Models\User $user, string $group): void
{
    // Grant all permissions for a specific group for testing using Spatie Permission
    // Use the same PermissionNameResolver as the seeder to ensure consistent naming
    $actions = ['view', 'create', 'update', 'delete'];
    $guardName = config('auth.defaults.guard', 'web');

    foreach ($actions as $action) {
        $permissionName = Mrmarchone\LaravelAutoCrud\Helpers\PermissionNameResolver::resolve($group, $action);
        $permissionModel = Spatie\Permission\Models\Permission::firstOrCreate(
            ['name' => $permissionName, 'guard_name' => $guardName]
        );
        $user->givePermissionTo($permissionModel);
    }

    // Clear permission cache to ensure permissions are immediately available
    app()[Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // Reload user relationships to ensure permissions are loaded
    $user->load('permissions');
}
