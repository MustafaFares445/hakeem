<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\SubscriptionAccessResult;
use App\Enums\SubscriptionAccessReasonEnum;
use App\Models\SubscriptionOrder;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Determines whether a user or tenant currently has access to the application
 * based on subscription state, trial period, and pending orders.*
 */
final readonly class SubscriptionAccessService
{
    /**
     * Resolve subscription access for a user via its tenant.
     */
    public function forUser(User $user): SubscriptionAccessResult
    {
        return $this->forTenant($user->tenant);
    }

    /**
     * Resolve subscription access for a tenant.
     *
     * Decision order (highest priority first):
     * 1. Tenant suspension
     * 2. Active lifetime subscription
     * 3. Active timed subscription
     * 4. Active trial
     * 5. Pending order
     * 6. Renewal required
     */
    public function forTenant(?Tenant $tenant): SubscriptionAccessResult
    {
        $now = CarbonImmutable::now();

        if ($tenant === null) {
            return $this->renewalRequired();
        }

        if ($tenant->is_suspended) {
            return $this->tenantSuspended($tenant);
        }

        $trialEndsAt = $this->trialEndsAt($tenant);

        if ($activeOrder = $this->activeOrder($tenant, $now)) {
            return $this->fromActiveOrder($activeOrder, $trialEndsAt);
        }

        if ($this->trialIsActive($trialEndsAt, $now)) {
            return $this->trialActive($trialEndsAt);
        }

        return $this->expiredWithPossiblePendingOrder($tenant, $trialEndsAt);
    }

    /**
     * Returns the highest-priority active subscription order for the tenant,
     * preferring lifetime to timed subscriptions.
     */
    private function activeOrder(Tenant $tenant, CarbonImmutable $now): ?SubscriptionOrder
    {
        return SubscriptionOrder::query()
            ->forTenant($tenant)
            ->confirmed()
            ->where(function (Builder $query) use ($now): void {
                $query
                    ->activeLifetime($now)
                    ->orWhere(fn (Builder $query) => $query->activeTimed($now));
            })
            ->orderByDesc('is_lifetime')
            ->orderByDesc('confirmed_at')
            ->orderByDesc('ends_at')
            ->first();
    }

    /**
     * Build an access result from an active subscription order.
     *
     * Lifetime subscriptions have no expiration.
     */
    private function fromActiveOrder(SubscriptionOrder $order, ?CarbonImmutable $trialEndsAt): SubscriptionAccessResult
    {
        if ($order->is_lifetime) {
            return new SubscriptionAccessResult(
                canUseApp: true,
                reason: SubscriptionAccessReasonEnum::LifetimeActive,
                trialEndsAt: $trialEndsAt,
                activeUntil: null,
                hasPendingOrder: false,
            );
        }

        return new SubscriptionAccessResult(
            canUseApp: true,
            reason: SubscriptionAccessReasonEnum::SubscriptionActive,
            trialEndsAt: $trialEndsAt,
            activeUntil: $order->ends_at?->toImmutable(),
            hasPendingOrder: false,
        );
    }

    /**
     * Determine whether the trial period is still valid at the given time.
     */
    private function trialIsActive(?CarbonImmutable $trialEndsAt, CarbonImmutable $now): bool
    {
        return $trialEndsAt !== null && $trialEndsAt->greaterThanOrEqualTo($now);
    }

    /**
     * Build an access result for an active trial.
     */
    private function trialActive(?CarbonImmutable $trialEndsAt): SubscriptionAccessResult
    {
        return new SubscriptionAccessResult(
            canUseApp: true,
            reason: SubscriptionAccessReasonEnum::TrialActive,
            trialEndsAt: $trialEndsAt,
            activeUntil: $trialEndsAt,
            hasPendingOrder: false,
        );
    }

    /**
     * Build an access result for an expired tenant, accounting for pending orders.
     */
    private function expiredWithPossiblePendingOrder(Tenant $tenant, ?CarbonImmutable $trialEndsAt): SubscriptionAccessResult
    {
        $hasPendingOrder = $this->hasPendingOrder($tenant);

        return new SubscriptionAccessResult(
            canUseApp: false,
            reason: $hasPendingOrder
                ? SubscriptionAccessReasonEnum::PendingConfirmation
                : SubscriptionAccessReasonEnum::RenewalRequired,
            trialEndsAt: $trialEndsAt,
            activeUntil: null,
            hasPendingOrder: $hasPendingOrder,
        );
    }

    /**
     * Check whether the tenant has any pending (unconfirmed) subscription orders.
     */
    private function hasPendingOrder(Tenant $tenant): bool
    {
        return SubscriptionOrder::query()
            ->forTenant($tenant)
            ->pending()
            ->exists();
    }

    /**
     * Normalize the tenant's trial end timestamp to an immutable instance.
     */
    private function trialEndsAt(Tenant $tenant): ?CarbonImmutable
    {
        return $tenant->trial_ends_at?->toImmutable();
    }

    /**
     * Build a renewal-required access result.
     */
    private function renewalRequired(): SubscriptionAccessResult
    {
        return new SubscriptionAccessResult(
            canUseApp: false,
            reason: SubscriptionAccessReasonEnum::RenewalRequired,
            trialEndsAt: null,
            activeUntil: null,
            hasPendingOrder: false,
        );
    }

    /**
     * Build a suspended-tenant access result.
     */
    private function tenantSuspended(Tenant $tenant): SubscriptionAccessResult
    {
        return new SubscriptionAccessResult(
            canUseApp: false,
            reason: SubscriptionAccessReasonEnum::TenantSuspended,
            trialEndsAt: $this->trialEndsAt($tenant),
            activeUntil: null,
            hasPendingOrder: false,
            suspensionReason: $tenant->suspension_reason,
        );
    }
}
