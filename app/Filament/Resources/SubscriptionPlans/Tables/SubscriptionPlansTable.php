<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubscriptionPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class SubscriptionPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenantType.name')
                    ->label('Tenant Type')
                    ->sortable(),

                TextColumn::make('price')
                    ->money(fn ($record): string => (string) $record->currency_code)
                    ->sortable(),

                TextColumn::make('duration_value')
                    ->label('Duration')
                    ->formatStateUsing(function ($record): string {
                        if ($record->is_lifetime) {
                            return 'Lifetime';
                        }

                        return sprintf('%d %s', (int) $record->duration_value, (string) $record->duration_unit?->value);
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tenant_type_id')
                    ->label('Tenant Type')
                    ->relationship('tenantType', 'name'),

                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
