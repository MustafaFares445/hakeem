<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\AdminActionLogService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('username')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('deactivate')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $adminUser = auth()->user();

                        if (! $adminUser instanceof User) {
                            return;
                        }

                        if (! $record->hasRole(RoleEnum::SystemAdmin->value)) {
                            return;
                        }

                        $record->removeRole(RoleEnum::SystemAdmin->value);

                        app(AdminActionLogService::class)->log(
                            action: 'admin_user.deactivated',
                            adminUser: $adminUser,
                            target: $record,
                            description: 'Admin user deactivated',
                        );

                        Notification::make()->success()->title('Admin user deactivated')->send();
                    }),
            ]);
    }
}
