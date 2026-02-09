<?php

declare(strict_types=1);

use App\Enums\SubscriptionAccessReasonEnum;
use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SubscriptionAccessService;

it('prioritizes active lifetime access over active timed access', function (): void {
    $tenant = Tenant::factory()->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $plan = SubscriptionPlan::factory()->create(['tenant_type_id' => $tenant->tenant_type_id]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $user->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed,
        'is_lifetime' => false,
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->addDays(30),
        'confirmed_at' => now(),
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $user->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed,
        'is_lifetime' => true,
        'starts_at' => now()->subDays(3),
        'ends_at' => null,
        'confirmed_at' => now()->subHour(),
    ]);

    $status = app(SubscriptionAccessService::class)->forTenant($tenant);

    expect($status->canUseApp)->toBeTrue()
        ->and($status->reason)->toBe(SubscriptionAccessReasonEnum::LifetimeActive)
        ->and($status->activeUntil)->toBeNull();
});

it('returns pending confirmation when trial is expired and pending order exists', function (): void {
    $tenant = Tenant::factory()->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $plan = SubscriptionPlan::factory()->create(['tenant_type_id' => $tenant->tenant_type_id]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $user->id,
        'status' => SubscriptionOrderStatusEnum::Pending,
    ]);

    $status = app(SubscriptionAccessService::class)->forTenant($tenant);

    expect($status->canUseApp)->toBeFalse()
        ->and($status->reason)->toBe(SubscriptionAccessReasonEnum::PendingConfirmation)
        ->and($status->hasPendingOrder)->toBeTrue();
});

it('does not leak orders across tenants', function (): void {
    $tenantA = Tenant::factory()->create([
        'trial_ends_at' => now()->subDay(),
    ]);
    $tenantB = Tenant::factory()->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
    $planA = SubscriptionPlan::factory()->create(['tenant_type_id' => $tenantA->tenant_type_id]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $tenantA->id,
        'subscription_plan_id' => $planA->id,
        'created_by_user_id' => $userA->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed,
        'is_lifetime' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => null,
        'confirmed_at' => now(),
    ]);

    $status = app(SubscriptionAccessService::class)->forTenant($tenantB);

    expect($status->canUseApp)->toBeFalse()
        ->and($status->reason)->toBe(SubscriptionAccessReasonEnum::RenewalRequired);
});
