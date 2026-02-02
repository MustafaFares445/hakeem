<?php

declare(strict_types=1);

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

it('paginates billings with default per page', function () {
    Billing::factory()->count(25)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/billings');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(20);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(20);
});

it('paginates billings with custom per page', function () {
    Billing::factory()->count(15)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/billings?perPage=5');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(5);
    expect($response->json('meta.per_page'))->toBe(5);
});

it('handles pagination for empty result set', function () {
    $response = $this->getJson('/api/billings');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
    expect($response->json('meta.total'))->toBe(0);
});
