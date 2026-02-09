<?php

declare(strict_types=1);

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenantType = TenantType::query()->where('key', 'small_clinic')->firstOrFail();

    $this->tenant = Tenant::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'trial_starts_at' => now(),
        'trial_ends_at' => now()->copy()->addMonth(),
    ]);

    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'username' => 'clinic_user',
        'password' => Hash::make('password123'),
    ]);

    grantPermissions($this->user, 'subscription_orders');
    grantPermissions($this->user, 'subscription_plans');

    Sanctum::actingAs($this->user);
});

it('returns public active tenant types', function (): void {
    $activeType = TenantType::factory()->create(['is_active' => true]);
    $inactiveType = TenantType::factory()->create(['is_active' => false]);

    $response = $this->getJson('/api/tenant-types');

    $response->assertOk();

    $keys = collect($response->json('data'))->pluck('id');

    expect($keys)->toContain($this->tenantType->id)
        ->and($keys)->toContain($activeType->id)
        ->and($keys)->not->toContain($inactiveType->id);
});

it('returns active subscription plans for tenant type only', function (): void {
    $visiblePlan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'is_active' => true,
    ]);

    SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'is_active' => false,
    ]);

    SubscriptionPlan::factory()->create([
        'tenant_type_id' => TenantType::factory()->create()->id,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/subscription-plans');

    $response->assertOk();

    $planIds = collect($response->json('data'))->pluck('id');

    expect($planIds)->toContain($visiblePlan->id)
        ->and($planIds)->toHaveCount(1);
});

it('creates subscription order with transaction image', function (): void {
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'is_active' => true,
    ]);

    $response = $this->post('/api/subscription-orders', [
        'subscriptionPlanId' => $plan->id,
        'transactionImage' => UploadedFile::fake()->image('proof.png'),
    ], ['Accept' => 'application/json']);

    $response->assertCreated();

    $orderId = $response->json('data.id');

    $this->assertDatabaseHas('subscription_orders', [
        'id' => $orderId,
        'tenant_id' => $this->tenant->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    $this->assertDatabaseHas('media', [
        'model_type' => SubscriptionOrder::class,
        'model_id' => $orderId,
        'collection_name' => 'transaction-proof',
    ]);
});

it('fails order creation when pending order already exists', function (): void {
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'is_active' => true,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    $response = $this->post('/api/subscription-orders', [
        'subscriptionPlanId' => $plan->id,
        'transactionImage' => UploadedFile::fake()->image('proof.png'),
    ], ['Accept' => 'application/json']);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['subscriptionPlanId']);
});

it('fails order creation when plan does not match tenant type', function (): void {
    $otherTenantType = TenantType::factory()->create();

    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $otherTenantType->id,
        'is_active' => true,
    ]);

    $response = $this->post('/api/subscription-orders', [
        'subscriptionPlanId' => $plan->id,
        'transactionImage' => UploadedFile::fake()->image('proof.png'),
    ], ['Accept' => 'application/json']);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['subscriptionPlanId']);
});

it('cancels pending subscription order', function (): void {
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'is_active' => true,
    ]);

    $order = SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    $response = $this->patchJson('/api/subscription-orders/'.$order->id.'/cancel');

    $response->assertOk()
        ->assertJsonPath('data.status', SubscriptionOrderStatusEnum::Cancelled->value);

    $this->assertDatabaseHas('subscription_orders', [
        'id' => $order->id,
        'status' => SubscriptionOrderStatusEnum::Cancelled->value,
    ]);
});

it('does not cancel non pending order', function (): void {
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'is_active' => true,
    ]);

    $order = SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed->value,
    ]);

    $response = $this->patchJson('/api/subscription-orders/'.$order->id.'/cancel');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['subscriptionOrderId']);
});

it('returns subscription order archive and supports status filter', function (): void {
    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'is_active' => true,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed->value,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Cancelled->value,
    ]);

    $all = $this->getJson('/api/subscription-orders');
    $all->assertOk();
    expect($all->json('data'))->toHaveCount(3);

    $pendingOnly = $this->getJson('/api/subscription-orders?filter[status]=pending');
    $pendingOnly->assertOk();

    $statuses = collect($pendingOnly->json('data'))->pluck('status')->unique()->values()->all();
    expect($statuses)->toBe([SubscriptionOrderStatusEnum::Pending->value]);
});

it('returns trial active subscription status', function (): void {
    $this->tenant->update([
        'trial_ends_at' => now()->copy()->addDays(5),
    ]);

    $response = $this->getJson('/api/subscription-status');

    $response->assertOk()
        ->assertJsonPath('data.canUseApp', true)
        ->assertJsonPath('data.reason', 'trial_active');
});

it('returns active timed subscription status', function (): void {
    $this->tenant->update([
        'trial_ends_at' => now()->copy()->subDay(),
    ]);

    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'is_lifetime' => false,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed->value,
        'starts_at' => now()->copy()->subDay(),
        'ends_at' => now()->copy()->addMonth(),
    ]);

    $response = $this->getJson('/api/subscription-status');

    $response->assertOk()
        ->assertJsonPath('data.canUseApp', true)
        ->assertJsonPath('data.reason', 'subscription_active');
});

it('returns active lifetime subscription status', function (): void {
    $this->tenant->update([
        'trial_ends_at' => now()->copy()->subDay(),
    ]);

    $plan = SubscriptionPlan::factory()->lifetime()->create([
        'tenant_type_id' => $this->tenantType->id,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Confirmed->value,
        'duration_value' => null,
        'duration_unit' => null,
        'is_lifetime' => true,
        'starts_at' => now()->copy()->subDay(),
        'ends_at' => null,
    ]);

    $response = $this->getJson('/api/subscription-status');

    $response->assertOk()
        ->assertJsonPath('data.canUseApp', true)
        ->assertJsonPath('data.reason', 'lifetime_active');
});

it('returns pending confirmation when trial expired and pending order exists', function (): void {
    $this->tenant->update([
        'trial_ends_at' => now()->copy()->subDay(),
    ]);

    $plan = SubscriptionPlan::factory()->create([
        'tenant_type_id' => $this->tenantType->id,
    ]);

    SubscriptionOrder::factory()->create([
        'tenant_id' => $this->tenant->id,
        'subscription_plan_id' => $plan->id,
        'created_by_user_id' => $this->user->id,
        'status' => SubscriptionOrderStatusEnum::Pending->value,
    ]);

    $response = $this->getJson('/api/subscription-status');

    $response->assertOk()
        ->assertJsonPath('data.canUseApp', false)
        ->assertJsonPath('data.reason', 'pending_confirmation');
});

it('returns renewal required when trial expired and no active subscription', function (): void {
    $this->tenant->update([
        'trial_ends_at' => now()->copy()->subDay(),
    ]);

    $response = $this->getJson('/api/subscription-status');

    $response->assertOk()
        ->assertJsonPath('data.canUseApp', false)
        ->assertJsonPath('data.reason', 'renewal_required');
});

it('blocks protected app routes with 402 when subscription inactive', function (): void {
    $this->tenant->update([
        'trial_ends_at' => now()->copy()->subDay(),
    ]);

    $response = $this->getJson('/api/clinic');

    $response->assertStatus(402)
        ->assertJsonPath('subscriptionStatus.canUseApp', false)
        ->assertJsonPath('subscriptionStatus.reason', 'renewal_required');
});

it('does not subscription block non tenant users', function (): void {
    $user = User::factory()->create([
        'tenant_id' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/users');

    $response->assertForbidden();
    expect($response->status())->not->toBe(402);
});
