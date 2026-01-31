<?php

declare(strict_types=1);

use App\Enums\BillingTypeEnum;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

beforeEach(/**
 * @throws JsonException
 * @throws TenantCouldNotBeIdentifiedById
 */ function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $tenant = Tenant::factory()->create();
    tenancy()->initialize($tenant);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    grantPermissions($user, 'billings');
    Sanctum::actingAs($user);
});

it('validates required fields when creating incoming billing', function () {
    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
    ];

    $response = $this->postJson('/api/billings', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['date', 'caseName', 'paidAmount', 'totalCost']);
});

it('validates required fields when creating outgoing billing', function () {
    $payload = [
        'type' => BillingTypeEnum::Outgoing->value,
        'date' => '2025-01-01',
    ];

    $response = $this->postJson('/api/billings', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['itemName', 'quantity', 'amount', 'outgoingType']);
});

it('validates type must be valid enum', function () {
    $payload = [
        'type' => 'invalid_type',
        'date' => '2025-01-01',
        'caseName' => 'Case',
        'paidAmount' => 100,
        'totalCost' => 200,
    ];

    $response = $this->postJson('/api/billings', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

it('validates date format', function () {
    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => 'invalid-date',
        'caseName' => 'Case',
        'paidAmount' => 100,
        'totalCost' => 200,
    ];

    $response = $this->postJson('/api/billings', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['date']);
});

it('validates paidAmount must be numeric and min 0', function () {
    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => '2025-01-01',
        'caseName' => 'Case',
        'paidAmount' => -10,
        'totalCost' => 200,
    ];

    $response = $this->postJson('/api/billings', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['paidAmount']);
});
