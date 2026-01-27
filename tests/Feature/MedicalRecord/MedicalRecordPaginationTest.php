<?php

declare(strict_types=1);

use App\Models\MedicalRecord;
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
    grantPermissions($user, 'medical_records');
    Sanctum::actingAs($user);
});

it('paginates medical records with default per page', function () {
    MedicalRecord::factory()->count(25)->create();

    $response = $this->getJson('/api/medical-records');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(20);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(20);
});

it('paginates medical records with custom per page', function () {
    MedicalRecord::factory()->count(15)->create();

    $response = $this->getJson('/api/medical-records?perPage=5');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(5);
    expect($response->json('meta.per_page'))->toBe(5);
});

it('handles pagination for empty result set', function () {
    $response = $this->getJson('/api/medical-records');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
    expect($response->json('meta.total'))->toBe(0);
});

it('handles pagination beyond last page', function () {
    MedicalRecord::factory()->count(5)->create();

    $response = $this->getJson('/api/medical-records?page=999');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
});

it('includes pagination metadata', function () {
    MedicalRecord::factory()->count(25)->create();

    $response = $this->getJson('/api/medical-records');

    $response->assertOk();
    expect($response->json('meta'))->toHaveKeys(['current_page', 'per_page', 'total', 'last_page']);
});

