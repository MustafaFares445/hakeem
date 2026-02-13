<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tenant Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Select::make('tenant_type_id')
                            ->label('Tenant Type')
                            ->relationship('tenantType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('domain_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone_number')
                            ->tel()
                            ->maxLength(25),

                        TextInput::make('phone_number2')
                            ->tel()
                            ->maxLength(25),

                        TextInput::make('city')
                            ->maxLength(255),

                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Trial')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('trial_starts_at')
                            ->seconds(false),

                        DateTimePicker::make('trial_ends_at')
                            ->seconds(false),
                    ]),
            ]);
    }
}
