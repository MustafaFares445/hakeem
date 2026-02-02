<?php

declare(strict_types=1);

use App\Enums\BillingTypeEnum;
use App\Models\Billing;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'billings');
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing billings', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Billing::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/billings');

    $response->assertForbidden();
});

it('forbids unauthorized user from creating billing', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => '2025-01-01',
        'caseName' => 'Case',
        'paidAmount' => 100,
        'totalCost' => 200,
    ];

    $response = $this->postJson('/api/billings', $payload);

    $response->assertForbidden();
});

it('forbids unauthorized user from updating billing', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $billing = Billing::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => '2025-01-01',
        'caseName' => 'Case',
        'paidAmount' => 100,
        'totalCost' => 200,
    ];

    $response = $this->putJson('/api/billings/'.$billing->id, $payload);

    $response->assertForbidden();
});

it('forbids unauthorized user from deleting billing', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $billing = Billing::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $response = $this->deleteJson('/api/billings/'.$billing->id);

    $response->assertForbidden();
});
