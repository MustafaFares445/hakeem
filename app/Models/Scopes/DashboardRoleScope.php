<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

final class DashboardRoleScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder<Role>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check() || ! Auth::user()->hasRole(config('permission.dashboard_role'))) {
            $builder->where('name', '!=', config('permission.dashboard_role'));
        }
    }
}
