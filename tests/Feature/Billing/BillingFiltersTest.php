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

it('filters billings by type', function () {
    Billing::factory()->incoming()->create(['tenant_id' => $this->tenant->id]);
    Billing::factory()->outgoing()->create(['tenant_id' => $this->tenant->id]);
    Billing::factory()->outgoing()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/billings?filter[type]=incoming');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.type'))->toBe(BillingTypeEnum::Incoming->value);
});

it('sorts billings by date', function () {
    Billing::factory()->create(['tenant_id' => $this->tenant->id, 'date' => '2025-01-01']);
    Billing::factory()->create(['tenant_id' => $this->tenant->id, 'date' => '2025-01-15']);

    $response = $this->getJson('/api/billings?sort=date');
    $response->assertOk();
    expect($response->json('data.0.date'))->toBe('2025-01-01');

    $response = $this->getJson('/api/billings?sort=-date');
    $response->assertOk();
    expect($response->json('data.0.date'))->toBe('2025-01-15');
});
