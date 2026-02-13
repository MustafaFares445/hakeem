<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\RoleEnum;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use UnitEnum;

final class Health extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static string|UnitEnum|null $navigationGroup = 'Governance';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Health';

    protected string $view = 'filament.pages.health';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->tenant_id === null
            && $user->roles()
                ->withoutGlobalScopes()
                ->where('name', RoleEnum::SystemAdmin->value)
                ->exists();
    }

    public function getFailedJobsCount(): int
    {
        if (! Schema::hasTable('failed_jobs')) {
            return 0;
        }

        return (int) DB::table('failed_jobs')->count();
    }

    public function getQueueBacklogCount(): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        return (int) DB::table('jobs')->count();
    }

    public function getPulseUrl(): string
    {
        return url('/'.mb_ltrim((string) config('pulse.path', 'pulse'), '/'));
    }

    public function getTelescopeUrl(): string
    {
        return url('/'.mb_ltrim((string) config('telescope.path', 'telescope'), '/'));
    }
}
