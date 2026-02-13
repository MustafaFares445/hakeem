<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\SubscriptionOrderData;
use App\Enums\SubscriptionDurationUnitEnum;
use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Mrmarchone\LaravelAutoCrud\Helpers\MediaHelper;
use RuntimeException;
use Throwable;

final readonly class SubscriptionOrderService
{
    public function __construct(
        private AdminActionLogService $adminActionLogService,
        private AdminNotificationService $adminNotificationService,
    ) {}

    /**
     * @throws Throwable
     */
    public function store(SubscriptionOrderData $data, SubscriptionPlan $plan): SubscriptionOrder
    {
        $order = DB::transaction(static function () use ($data, $plan): SubscriptionOrder {
            $order = SubscriptionOrder::query()->create([
                'subscription_plan_id' => $data->subscriptionPlanId,
                'created_by_user_id' => Auth::id(),
                'status' => SubscriptionOrderStatusEnum::Pending->value,
                'plan_name' => $plan->name,
                'plan_description' => $plan->description,
                'original_price' => $plan->original_price,
                'price' => $plan->price,
                'currency_code' => $plan->currency_code,
                'duration_value' => $plan->duration_value,
                'duration_unit' => $plan->duration_unit?->value,
                'is_lifetime' => $plan->is_lifetime,
            ]);

            MediaHelper::uploadMedia($data->transactionImage, $order, 'transaction-proof');

            return $order->load('media');
        });

        $this->adminNotificationService->notifyNewPendingOrder($order);

        return $order;
    }

    /**
     * @throws Throwable
     */
    public function update(SubscriptionOrder $order): SubscriptionOrder
    {
        return DB::transaction(static function () use ($order): SubscriptionOrder {
            tap($order)->update([
                'status' => SubscriptionOrderStatusEnum::Cancelled->value,
                'cancelled_at' => now(),
            ]);

            return $order->fresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function confirm(SubscriptionOrder $order, User $adminUser): SubscriptionOrder
    {
        $this->ensurePending($order);

        return DB::transaction(function () use ($order, $adminUser): SubscriptionOrder {
            $now = CarbonImmutable::now();
            $startsAt = $this->resolveStartsAt($order, $now);

            $order->update([
                'status' => SubscriptionOrderStatusEnum::Confirmed->value,
                'starts_at' => $startsAt,
                'ends_at' => $this->resolveEndsAt($order, $startsAt),
                'confirmed_at' => $now,
                'confirmed_by_user_id' => $adminUser->id,
                'cancelled_at' => null,
                'cancelled_by_user_id' => null,
                'cancellation_reason' => null,
            ]);

            $this->adminActionLogService->log(
                action: 'subscription_order.confirmed',
                adminUser: $adminUser,
                tenantId: $order->tenant_id,
                target: $order,
                description: 'Subscription order confirmed by admin',
                metadata: [
                    'starts_at' => $startsAt->toIso8601String(),
                    'ends_at' => $order->fresh()?->ends_at?->toIso8601String(),
                    'is_lifetime' => $order->is_lifetime,
                ],
            );

            return $order->fresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function reject(SubscriptionOrder $order, User $adminUser, string $reason): SubscriptionOrder
    {
        $this->ensurePending($order);

        $reason = mb_trim($reason);

        if ($reason === '') {
            throw new InvalidArgumentException('Rejection reason is required.');
        }

        return DB::transaction(function () use ($order, $adminUser, $reason): SubscriptionOrder {
            $order->update([
                'status' => SubscriptionOrderStatusEnum::Cancelled->value,
                'confirmed_at' => null,
                'confirmed_by_user_id' => null,
                'starts_at' => null,
                'ends_at' => null,
                'cancelled_at' => now(),
                'cancelled_by_user_id' => $adminUser->id,
                'cancellation_reason' => $reason,
            ]);

            $this->adminActionLogService->log(
                action: 'subscription_order.rejected',
                adminUser: $adminUser,
                tenantId: $order->tenant_id,
                target: $order,
                description: 'Subscription order rejected by admin',
                metadata: [
                    'reason' => $reason,
                ],
            );

            return $order->fresh();
        });
    }

    private function ensurePending(SubscriptionOrder $order): void
    {
        if ($order->status !== SubscriptionOrderStatusEnum::Pending) {
            throw new RuntimeException('Only pending subscription orders can be moderated.');
        }
    }

    private function resolveStartsAt(SubscriptionOrder $order, CarbonImmutable $now): CarbonImmutable
    {
        if ($order->is_lifetime) {
            return $now;
        }

        $latestTimedOrder = SubscriptionOrder::query()
            ->where('tenant_id', $order->tenant_id)
            ->where('status', SubscriptionOrderStatusEnum::Confirmed->value)
            ->where('is_lifetime', false)
            ->whereNotNull('ends_at')
            ->where('id', '!=', $order->id)
            ->where('ends_at', '>=', $now)
            ->orderByDesc('ends_at')
            ->first();

        $existingEndsAt = $latestTimedOrder?->ends_at?->toImmutable();

        if ($existingEndsAt === null) {
            return $now;
        }

        return $existingEndsAt->greaterThan($now)
            ? $existingEndsAt
            : $now;
    }

    private function resolveEndsAt(SubscriptionOrder $order, CarbonImmutable $startsAt): ?CarbonImmutable
    {
        if ($order->is_lifetime) {
            return null;
        }

        if (! is_int($order->duration_value) || $order->duration_value <= 0) {
            throw new RuntimeException('Timed subscription orders must have a valid duration value.');
        }

        if (! $order->duration_unit instanceof SubscriptionDurationUnitEnum) {
            throw new RuntimeException('Timed subscription orders must have a valid duration unit.');
        }

        return match ($order->duration_unit) {
            SubscriptionDurationUnitEnum::Day => $startsAt->addDays($order->duration_value),
            SubscriptionDurationUnitEnum::Month => $startsAt->addMonthsNoOverflow($order->duration_value),
            SubscriptionDurationUnitEnum::Year => $startsAt->addYearsNoOverflow($order->duration_value),
        };
    }
}
