<?php

declare(strict_types=1);

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'medical_records');
    Sanctum::actingAs($user);
});

it('paginates medical records with default per page', function () {
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    MedicalRecord::factory()->count(25)->create(['tenant_id' => $this->tenant->id, 'patient_id' => $patient->id]);

    $response = $this->getJson('/api/medical-records');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(20);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(20);
});

it('paginates medical records with custom per page', function () {
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    MedicalRecord::factory()->count(15)->create(['tenant_id' => $this->tenant->id, 'patient_id' => $patient->id]);

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
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    MedicalRecord::factory()->count(5)->create(['tenant_id' => $this->tenant->id, 'patient_id' => $patient->id]);

    $response = $this->getJson('/api/medical-records?page=999');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
});

it('includes pagination metadata', function () {
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    MedicalRecord::factory()->count(25)->create(['tenant_id' => $this->tenant->id, 'patient_id' => $patient->id]);

    $response = $this->getJson('/api/medical-records');

    $response->assertOk();
    expect($response->json('meta'))->toHaveKeys(['current_page', 'per_page', 'total', 'last_page']);
});
