<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

final class AdminOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $now = CarbonImmutable::now();
        $endingSoonThresholdDays = 7;
        $endingSoonAt = $now->addDays($endingSoonThresholdDays);

        return [
            Stat::make('Total Tenants', (string) Tenant::query()->count()),

            Stat::make(
                'Active Subscriptions',
                (string) SubscriptionOrder::query()
                    ->where('status', SubscriptionOrderStatusEnum::Confirmed->value)
                    ->where(function (Builder $query) use ($now): void {
                        $query
                            ->where(function (Builder $query) use ($now): void {
                                $query
                                    ->where('is_lifetime', true)
                                    ->where(function (Builder $query) use ($now): void {
                                        $query->whereNull('starts_at')
                                            ->orWhere('starts_at', '<=', $now);
                                    });
                            })
                            ->orWhere(function (Builder $query) use ($now): void {
                                $query
                                    ->where('is_lifetime', false)
                                    ->where('starts_at', '<=', $now)
                                    ->where('ends_at', '>=', $now);
                            });
                    })
                    ->distinct('tenant_id')
                    ->count('tenant_id')
            ),

            Stat::make(
                'Trials Ending Soon',
                (string) Tenant::query()
                    ->whereNotNull('trial_ends_at')
                    ->whereBetween('trial_ends_at', [$now, $endingSoonAt])
                    ->count()
            )
                ->description("Next {$endingSoonThresholdDays} days"),

            Stat::make(
                'Pending Orders',
                (string) SubscriptionOrder::query()
                    ->where('status', SubscriptionOrderStatusEnum::Pending->value)
                    ->count()
            ),

            Stat::make(
                'Cancelled This Month',
                (string) SubscriptionOrder::query()
                    ->where('status', SubscriptionOrderStatusEnum::Cancelled->value)
                    ->whereBetween('cancelled_at', [$now->startOfMonth(), $now->endOfMonth()])
                    ->count()
            ),
        ];
    }
}
