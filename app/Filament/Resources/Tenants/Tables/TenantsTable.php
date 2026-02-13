<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tenants\Tables;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantAdministrationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenantType.name')
                    ->label('Tenant Type')
                    ->sortable(),

                TextColumn::make('domain_name')
                    ->searchable(),

                TextColumn::make('trial_ends_at')
                    ->label('Trial Ends')
                    ->dateTime(),

                IconColumn::make('is_suspended')
                    ->label('Suspended')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tenant_type_id')
                    ->label('Tenant Type')
                    ->relationship('tenantType', 'name'),

                TernaryFilter::make('is_suspended')
                    ->label('Suspended'),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('suspend')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => ! $record->is_suspended)
                    ->form([
                        Textarea::make('reason')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000),
                    ])
                    ->action(function (Tenant $record, array $data): void {
                        $adminUser = auth()->user();

                        if (! $adminUser instanceof User) {
                            return;
                        }

                        app(TenantAdministrationService::class)->suspend($record, (string) $data['reason'], $adminUser);

                        Notification::make()->success()->title('Tenant suspended successfully')->send();
                    }),

                Action::make('reactivate')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => $record->is_suspended)
                    ->action(function (Tenant $record): void {
                        $adminUser = auth()->user();

                        if (! $adminUser instanceof User) {
                            return;
                        }

                        app(TenantAdministrationService::class)->reactivate($record, $adminUser);

                        Notification::make()->success()->title('Tenant reactivated successfully')->send();
                    }),

                Action::make('extend_trial')
                    ->label('Extend Trial')
                    ->color('warning')
                    ->form([
                        TextInput::make('days')
                            ->numeric()
                            ->minValue(1)
                            ->default(7)
                            ->required(),
                    ])
                    ->action(function (Tenant $record, array $data): void {
                        $adminUser = auth()->user();

                        if (! $adminUser instanceof User) {
                            return;
                        }

                        app(TenantAdministrationService::class)->extendTrial($record, (int) $data['days'], $adminUser);

                        Notification::make()->success()->title('Trial extended successfully')->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
