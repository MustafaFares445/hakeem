<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Scopes\DashboardRoleScope;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {

            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);

            $this->app->register(TelescopeServiceProvider::class);

        }
    }

    public function boot(): void
    {
        $this->bootModelsDefaults();

        if (! app()->runningInConsole()) {
            Role::addGlobalScopes([DashboardRoleScope::class]);
        }

        Gate::define('attempt-login', static function (User $user, string $password): bool {
            if (! Hash::check($password, $user->password)) {
                throw new AuthenticationException(__('Invalid credentials.'));
            }

            $tenantId = tenant()?->getKey();

            if ($tenantId !== null && $user->tenant_id !== $tenantId) {
                throw new AuthenticationException(__('Invalid credentials.'));
            }

            return true;
        });
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
    }
}
