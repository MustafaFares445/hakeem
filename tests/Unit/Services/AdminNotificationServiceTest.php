<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\NewPendingSubscriptionOrderNotification;
use App\Notifications\SubscriptionEndingSoonNotification;
use App\Notifications\TrialEndingSoonNotification;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    Cache::flush();
});

it('sends new pending order notifications to system admins', function (): void {
    Notification::fake();

    $admin = User::factory()->create(['tenant_id' => null]);
    $admin->assignRole(RoleEnum::SystemAdmin->value);

    $tenant = Tenant::factory()->create();
    $tenantUser = User::factory()->create(['tenant_id' => $tenant->id]);

    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $tenant->tenant_type_id,
    ]);

    $order = SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $tenantUser->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    app(AdminNotificationService::class)->notifyNewPendingOrder($order);

    Notification::assertSentTo($admin, NewPendingSubscriptionOrderNotification::class);
    Notification::assertNotSentTo($tenantUser, NewPendingSubscriptionOrderNotification::class);
});

it('dispatches ending-soon alerts to system admins', function (): void {
    Notification::fake();

    $admin = User::factory()->create(['tenant_id' => null]);
    $admin->assignRole(RoleEnum::SystemAdmin->value);

    $tenant = Tenant::factory()->create([
        'trial_ends_at' => now()->copy()->addDays(3),
    ]);

    $tenantUser = User::factory()->create(['tenant_id' => $tenant->id]);

    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $tenant->tenant_type_id,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $tenantUser->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed->value,
        'is_lifetime' => false,
        'starts_at' => now()->copy()->subDay(),
        'ends_at' => now()->copy()->addDays(2),
    ]);

    app(AdminNotificationService::class)->dispatchEndingSoonAlerts(7);

    Notification::assertSentTo($admin, TrialEndingSoonNotification::class);
    Notification::assertSentTo($admin, SubscriptionEndingSoonNotification::class);
});
