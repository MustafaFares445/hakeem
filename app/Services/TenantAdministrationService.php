<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class TenantAdministrationService
{
    public function __construct(private AdminActionLogService $adminActionLogService) {}

    /**
     * @throws Throwable
     */
    public function suspend(Tenant $tenant, string $reason, User $adminUser): Tenant
    {
        return DB::transaction(function () use ($tenant, $reason, $adminUser): Tenant {
            $tenant->update([
                'is_suspended' => true,
                'suspended_at' => now(),
                'suspension_reason' => mb_trim($reason),
                'suspended_by_user_id' => $adminUser->id,
            ]);

            $this->adminActionLogService->log(
                action: 'tenant.suspended',
                adminUser: $adminUser,
                tenantId: $tenant->id,
                target: $tenant,
                description: 'Tenant suspended by admin',
                metadata: [
                    'reason' => mb_trim($reason),
                ],
            );

            return $tenant->fresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function reactivate(Tenant $tenant, User $adminUser): Tenant
    {
        return DB::transaction(function () use ($tenant, $adminUser): Tenant {
            $tenant->update([
                'is_suspended' => false,
                'suspended_at' => null,
                'suspension_reason' => null,
                'suspended_by_user_id' => null,
            ]);

            $this->adminActionLogService->log(
                action: 'tenant.reactivated',
                adminUser: $adminUser,
                tenantId: $tenant->id,
                target: $tenant,
                description: 'Tenant reactivated by admin',
            );

            return $tenant->fresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function extendTrial(Tenant $tenant, int $days, User $adminUser): Tenant
    {
        return DB::transaction(function () use ($tenant, $days, $adminUser): Tenant {
            $trialEndsAt = $tenant->trial_ends_at?->copy() ?? now();
            $nextTrialEndsAt = $trialEndsAt->addDays($days);

            $tenant->update([
                'trial_ends_at' => $nextTrialEndsAt,
            ]);

            $this->adminActionLogService->log(
                action: 'tenant.trial_extended',
                adminUser: $adminUser,
                tenantId: $tenant->id,
                target: $tenant,
                description: 'Tenant trial extended by admin',
                metadata: [
                    'days' => $days,
                    'trial_ends_at' => $nextTrialEndsAt->toIso8601String(),
                ],
            );

            return $tenant->fresh();
        });
    }
}
