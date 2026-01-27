<?php

declare(strict_types=1);

use App\Enums\RecordTypeEnum;
use App\Models\Patient;
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

it('validates required fields when creating a medical record', function () {
    $payload = [];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId', 'recordDate', 'recordType', 'caseName']);
});

it('validates patientId must be a valid existing uuid', function () {
    $payload = [
        'patientId' => 'invalid-uuid',
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Sample case',
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId']);

    $payload['patientId'] = '00000000-0000-0000-0000-000000000000';

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId']);
});

it('validates recordDate must be a valid date format', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => 'invalid-date',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Sample case',
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['recordDate']);
});

it('validates recordType must be a valid enum value', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => 'invalid-enum',
        'caseName' => 'Sample case',
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['recordType']);
});

it('validates caseName must not exceed max length', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => str_repeat('a', 300),
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['caseName']);
});

it('validates description must not exceed max length', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Sample case',
        'description' => str_repeat('a', 2000),
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['description']);
});

it('validates totalCost and remainingAmount must be non negative numbers', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Sample case',
        'totalCost' => -10,
        'remainingAmount' => -5,
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['totalCost', 'remainingAmount']);
});

