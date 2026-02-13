<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdminActionLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class AdminActionLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('action')
                    ->badge()
                    ->searchable(),

                TextColumn::make('adminUser.name')
                    ->label('Admin User')
                    ->searchable(),

                TextColumn::make('tenant_id')
                    ->label('Tenant')
                    ->searchable(),

                TextColumn::make('target_type')
                    ->label('Target')
                    ->formatStateUsing(static fn (?string $state): string => $state !== null ? class_basename($state) : '-')
                    ->toggleable(),

                TextColumn::make('target_id')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->limit(60)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options(static function (): array {
                        return \App\Models\AdminActionLog::query()
                            ->distinct()
                            ->orderBy('action')
                            ->pluck('action', 'action')
                            ->all();
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
