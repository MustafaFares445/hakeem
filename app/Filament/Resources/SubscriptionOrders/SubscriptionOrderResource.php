<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubscriptionOrders;

use App\Filament\Resources\AdminResource;
use App\Filament\Resources\SubscriptionOrders\Pages\ListSubscriptionOrders;
use App\Filament\Resources\SubscriptionOrders\Schemas\SubscriptionOrderForm;
use App\Filament\Resources\SubscriptionOrders\Tables\SubscriptionOrdersTable;
use App\Models\SubscriptionOrder;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class SubscriptionOrderResource extends AdminResource
{
    protected static ?string $model = SubscriptionOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $navigationLabel = 'Subscription Orders';

    protected static string|UnitEnum|null $navigationGroup = 'Subscriptions';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return SubscriptionOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionOrders::route('/'),
        ];
    }
}
