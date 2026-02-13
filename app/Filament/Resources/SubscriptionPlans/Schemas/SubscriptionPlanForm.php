<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubscriptionPlans\Schemas;

use App\Enums\SubscriptionDurationUnitEnum;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

final class SubscriptionPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plan Details')
                    ->columns(2)
                    ->schema([
                        Select::make('tenant_type_id')
                            ->label('Tenant Type')
                            ->relationship('tenantType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),

                Section::make('Pricing')
                    ->columns(3)
                    ->schema([
                        TextInput::make('original_price')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('currency_code')
                            ->default('USD')
                            ->length(3)
                            ->required(),
                    ]),

                Section::make('Duration')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_lifetime')
                            ->default(false)
                            ->live(),

                        TextInput::make('duration_value')
                            ->numeric()
                            ->minValue(1)
                            ->required(fn (Get $get): bool => ! (bool) $get('is_lifetime'))
                            ->hidden(fn (Get $get): bool => (bool) $get('is_lifetime')),

                        Select::make('duration_unit')
                            ->options([
                                SubscriptionDurationUnitEnum::Day->value => 'Day',
                                SubscriptionDurationUnitEnum::Month->value => 'Month',
                                SubscriptionDurationUnitEnum::Year->value => 'Year',
                            ])
                            ->required(fn (Get $get): bool => ! (bool) $get('is_lifetime'))
                            ->hidden(fn (Get $get): bool => (bool) $get('is_lifetime')),

                        Placeholder::make('lifetime_hint')
                            ->label('')
                            ->content('Lifetime plans do not require duration value or duration unit.')
                            ->hidden(fn (Get $get): bool => ! (bool) $get('is_lifetime'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
