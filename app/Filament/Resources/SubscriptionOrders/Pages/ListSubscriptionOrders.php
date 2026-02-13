<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubscriptionOrders\Pages;

use App\Filament\Resources\SubscriptionOrders\SubscriptionOrderResource;
use Filament\Resources\Pages\ListRecords;

final class ListSubscriptionOrders extends ListRecords
{
    protected static string $resource = SubscriptionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
