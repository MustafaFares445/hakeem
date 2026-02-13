<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\RoleEnum;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

abstract class AdminResource extends Resource
{
    final public static function canAccess(): bool
    {
        return static::currentUserIsSystemAdmin();
    }

    final public static function canViewAny(): bool
    {
        return static::currentUserIsSystemAdmin();
    }

    final public static function canView(Model $record): bool
    {
        return static::currentUserIsSystemAdmin();
    }

    final public static function canCreate(): bool
    {
        return static::currentUserIsSystemAdmin();
    }

    final public static function canEdit(Model $record): bool
    {
        return static::currentUserIsSystemAdmin();
    }

    final public static function canDelete(Model $record): bool
    {
        return static::currentUserIsSystemAdmin();
    }

    final public static function canDeleteAny(): bool
    {
        return static::currentUserIsSystemAdmin();
    }

    protected static function currentUserIsSystemAdmin(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->tenant_id === null
            && $user->roles()
                ->withoutGlobalScopes()
                ->where('name', RoleEnum::SystemAdmin->value)
                ->exists();
    }
}
