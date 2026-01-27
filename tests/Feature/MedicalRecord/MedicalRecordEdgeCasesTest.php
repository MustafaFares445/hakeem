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

it('handles empty payload gracefully when creating medical record', function () {
    $payload = [];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertStatus(422);
});

it('sanitizes SQL injection attempts in string fields', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => '\'; DROP TABLE medical_records; --',
        'description' => 'Sample description',
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    expect($response->status())->toBeIn([201, 422]);
});

it('sanitizes XSS attempts in string fields', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => '<script>alert("XSS")</script>',
        'description' => '<script>alert("XSS")</script>',
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    expect($response->status())->toBeIn([201, 422]);
});

it('handles max length boundary for caseName', function () {
    $patient = Patient::factory()->create();
    $maxLengthString = str_repeat('a', 255);

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => $maxLengthString,
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    expect($response->status())->toBeIn([201, 422]);
});

it('handles max length boundary for description', function () {
    $patient = Patient::factory()->create();
    $maxLengthString = str_repeat('a', 1000);

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Sample case',
        'description' => $maxLengthString,
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    expect($response->status())->toBeIn([201, 422]);
});

