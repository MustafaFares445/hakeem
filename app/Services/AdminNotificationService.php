<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\NewPendingSubscriptionOrderNotification;
use App\Notifications\SubscriptionEndingSoonNotification;
use App\Notifications\TrialEndingSoonNotification;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

final class AdminNotificationService
{
    private const DEFAULT_ENDING_SOON_DAYS = 7;

    public function notifyNewPendingOrder(SubscriptionOrder $order): void
    {
        $order->loadMissing('tenant');

        $admins = $this->systemAdmins();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new NewPendingSubscriptionOrderNotification($order));
    }

    public function dispatchEndingSoonAlerts(int $days = self::DEFAULT_ENDING_SOON_DAYS): void
    {
        $admins = $this->systemAdmins();

        if ($admins->isEmpty()) {
            return;
        }

        $this->notifyTrialsEndingSoon($admins, $days);
        $this->notifySubscriptionsEndingSoon($admins, $days);
    }

    /**
     * @param  Collection<int, User>  $admins
     */
    private function notifyTrialsEndingSoon(Collection $admins, int $days): void
    {
        $now = CarbonImmutable::now();
        $deadline = $now->addDays($days);

        $tenants = Tenant::query()
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [$now, $deadline])
            ->get();

        foreach ($tenants as $tenant) {
            if ($tenant->trial_ends_at === null) {
                continue;
            }

            $cacheKey = sprintf(
                'admin:trial-ending:%s:%s',
                $tenant->id,
                $tenant->trial_ends_at->toDateString(),
            );

            if (! Cache::add($cacheKey, true, $now->endOfDay())) {
                continue;
            }

            $daysRemaining = max((int) $now->diffInDays($tenant->trial_ends_at, false), 0);

            Notification::send($admins, new TrialEndingSoonNotification($tenant, $daysRemaining));
        }
    }

    /**
     * @param  Collection<int, User>  $admins
     */
    private function notifySubscriptionsEndingSoon(Collection $admins, int $days): void
    {
        $now = CarbonImmutable::now();
        $deadline = $now->addDays($days);

        $orders = SubscriptionOrder::query()
            ->with('tenant')
            ->where('status', SubscriptionOrderStatusEnum::Confirmed->value)
            ->where('is_lifetime', false)
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [$now, $deadline])
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->get();

        foreach ($orders as $order) {
            if ($order->ends_at === null) {
                continue;
            }

            $cacheKey = sprintf(
                'admin:subscription-ending:%s:%s',
                $order->id,
                $order->ends_at->toDateString(),
            );

            if (! Cache::add($cacheKey, true, $now->endOfDay())) {
                continue;
            }

            $daysRemaining = max((int) $now->diffInDays($order->ends_at, false), 0);

            Notification::send($admins, new SubscriptionEndingSoonNotification($order, $daysRemaining));
        }
    }

    /**
     * @return Collection<int, User>
     */
    private function systemAdmins(): Collection
    {
        return User::query()
            ->whereNull('tenant_id')
            ->whereHas('roles', static function (Builder $query): void {
                $query->where('name', RoleEnum::SystemAdmin->value);
            })
            ->get();
    }
}
