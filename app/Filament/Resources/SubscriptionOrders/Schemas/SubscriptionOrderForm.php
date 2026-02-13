<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubscriptionOrders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class SubscriptionOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order')
                    ->columns(2)
                    ->schema([
                        TextInput::make('tenant.name')
                            ->label('Tenant')
                            ->disabled(),

                        TextInput::make('status')
                            ->disabled(),

                        TextInput::make('plan_name')
                            ->disabled(),

                        TextInput::make('price')
                            ->disabled(),

                        DateTimePicker::make('starts_at')
                            ->disabled(),

                        DateTimePicker::make('ends_at')
                            ->disabled(),
                    ]),
            ]);
    }
}
