<?php

declare(strict_types=1);

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SubscriptionOrderService;

it('confirms pending orders and writes moderation metadata', function (): void {
    $tenant = Tenant::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $tenant->tenant_type_id,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'is_lifetime' => false,
    ]);

    $admin = User::factory()->create(['tenant_id' => null]);
    $tenantUser = User::factory()->create(['tenant_id' => $tenant->id]);

    $order = SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $tenantUser->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'is_lifetime' => false,
    ]);

    $confirmed = app(SubscriptionOrderService::class)->confirm($order, $admin);

    $expectedEndAt = now()->copy()->addMonthsNoOverflow(1);

    expect($confirmed->status)->toBe(SubscriptionOrderStatusEnum::Confirmed)
        ->and($confirmed->confirmed_by_user_id)->toBe($admin->id)
        ->and($confirmed->confirmed_at)->not->toBeNull()
        ->and($confirmed->starts_at)->not->toBeNull()
        ->and($confirmed->confirmed_at?->toDateTimeString())->toBe(now()->toDateTimeString())
        ->and($confirmed->starts_at?->toDateTimeString())->toBe(now()->toDateTimeString())
        ->and($confirmed->ends_at?->toDateTimeString())->toBe($expectedEndAt->toDateTimeString());

    $this->assertDatabaseHas('admin_action_logs', [
        'action' => 'subscription_order.confirmed',
        'tenant_id' => $tenant->id,
        'target_id' => $order->id,
    ]);
});

it('queues timed confirmations after currently active timed orders', function (): void {
    $tenant = Tenant::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $tenant->tenant_type_id,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'is_lifetime' => false,
    ]);

    $admin = User::factory()->create(['tenant_id' => null]);
    $tenantUser = User::factory()->create(['tenant_id' => $tenant->id]);

    $activeOrder = SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $tenantUser->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed->value,
        'starts_at' => now()->copy()->subDays(10),
        'ends_at' => now()->copy()->addDays(10),
        'confirmed_at' => now()->copy()->subDays(10),
    ]);

    $pendingOrder = SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $tenantUser->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'is_lifetime' => false,
    ]);

    $confirmed = app(SubscriptionOrderService::class)->confirm($pendingOrder, $admin);

    $expectedEndAt = $activeOrder->ends_at?->copy()->addMonthsNoOverflow(1);

    expect($confirmed->starts_at?->toDateTimeString())->toBe($activeOrder->ends_at?->toDateTimeString())
        ->and($confirmed->ends_at?->toDateTimeString())->toBe($expectedEndAt?->toDateTimeString());
});

it('reject requires a reason', function (): void {
    $tenant = Tenant::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $tenant->tenant_type_id,
    ]);

    $admin = User::factory()->create(['tenant_id' => null]);
    $tenantUser = User::factory()->create(['tenant_id' => $tenant->id]);

    $order = SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $tenantUser->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    expect(fn () => app(SubscriptionOrderService::class)->reject($order, $admin, '   '))
        ->toThrow(InvalidArgumentException::class);
});

it('rejects pending orders and stores cancellation metadata', function (): void {
    $tenant = Tenant::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $tenant->tenant_type_id,
    ]);

    $admin = User::factory()->create(['tenant_id' => null]);
    $tenantUser = User::factory()->create(['tenant_id' => $tenant->id]);

    $order = SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $tenantUser->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    $rejected = app(SubscriptionOrderService::class)->reject($order, $admin, 'Invalid transaction proof');

    expect($rejected->status)->toBe(SubscriptionOrderStatusEnum::Cancelled)
        ->and($rejected->cancelled_by_user_id)->toBe($admin->id)
        ->and($rejected->cancellation_reason)->toBe('Invalid transaction proof')
        ->and($rejected->cancelled_at)->not->toBeNull();

    $this->assertDatabaseHas('admin_action_logs', [
        'action' => 'subscription_order.rejected',
        'tenant_id' => $tenant->id,
        'target_id' => $order->id,
    ]);
});
