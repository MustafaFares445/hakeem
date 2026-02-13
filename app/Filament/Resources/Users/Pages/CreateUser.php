<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleEnum;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Services\AdminActionLogService;
use Filament\Resources\Pages\CreateRecord;

final class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record?->assignRole(RoleEnum::SystemAdmin->value);

        $adminUser = auth()->user();

        if (! $adminUser instanceof User || ! $this->record instanceof User) {
            return;
        }

        app(AdminActionLogService::class)->log(
            action: 'admin_user.created',
            adminUser: $adminUser,
            target: $this->record,
            description: 'Admin user created',
        );
    }
}
