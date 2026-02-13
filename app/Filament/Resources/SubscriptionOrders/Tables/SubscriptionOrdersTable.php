<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubscriptionOrders\Tables;

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\User;
use App\Services\SubscriptionOrderService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class SubscriptionOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(static fn (Builder $query): Builder => $query->with(['tenant', 'media']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable(),

                TextColumn::make('plan_name')
                    ->label('Plan')
                    ->searchable(),

                TextColumn::make('price')
                    ->money(fn (SubscriptionOrder $record): string => $record->currency_code),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        SubscriptionOrderStatusEnum::Pending->value => 'warning',
                        SubscriptionOrderStatusEnum::Confirmed->value => 'success',
                        SubscriptionOrderStatusEnum::Cancelled->value => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->dateTime(),

                TextColumn::make('ends_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        SubscriptionOrderStatusEnum::Pending->value => 'Pending',
                        SubscriptionOrderStatusEnum::Confirmed->value => 'Confirmed',
                        SubscriptionOrderStatusEnum::Cancelled->value => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                Action::make('proof')
                    ->label('Proof')
                    ->url(function (SubscriptionOrder $record): ?string {
                        $url = $record->getFirstMediaUrl('transaction-proof');

                        return $url !== '' ? $url : null;
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (SubscriptionOrder $record): bool => $record->hasMedia('transaction-proof')),

                Action::make('confirm')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SubscriptionOrder $record): bool => $record->status === SubscriptionOrderStatusEnum::Pending)
                    ->action(function (SubscriptionOrder $record): void {
                        $adminUser = auth()->user();

                        if (! $adminUser instanceof User) {
                            return;
                        }

                        app(SubscriptionOrderService::class)->confirm($record, $adminUser);

                        Notification::make()->success()->title('Subscription order confirmed')->send();
                    }),

                Action::make('reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (SubscriptionOrder $record): bool => $record->status === SubscriptionOrderStatusEnum::Pending)
                    ->form([
                        Textarea::make('reason')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000),
                    ])
                    ->action(function (SubscriptionOrder $record, array $data): void {
                        $adminUser = auth()->user();

                        if (! $adminUser instanceof User) {
                            return;
                        }

                        app(SubscriptionOrderService::class)->reject($record, $adminUser, (string) $data['reason']);

                        Notification::make()->success()->title('Subscription order rejected')->send();
                    }),
            ]);
    }
}
