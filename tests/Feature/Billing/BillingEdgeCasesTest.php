<?php

declare(strict_types=1);

use App\Enums\BillingTypeEnum;
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

it('handles empty payload gracefully', function () {
    $response = $this->postJson('/api/billings', []);

    $response->assertStatus(422);
});

it('accepts incoming billing with optional null patientId and userId', function () {
    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => '2025-01-01',
        'patientId' => null,
        'userId' => null,
        'caseName' => 'Case',
        'paidAmount' => 100,
        'totalCost' => 200,
    ];

    $response = $this->postJson('/api/billings', $payload);

    $response->assertCreated();
});
