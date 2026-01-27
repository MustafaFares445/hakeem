<?php

declare(strict_types=1);

use App\Enums\RecordTypeEnum;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
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

it('lists medical records', function () {
    MedicalRecord::factory()->count(3)->create();

    $response = $this->getJson('/api/medical-records');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];

        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('patientId')
            ->and($firstItem)->toHaveKey('recordDate')
            ->and($firstItem)->toHaveKey('recordType')
            ->and($firstItem)->toHaveKey('caseName')
            ->and($firstItem)->toHaveKey('description')
            ->and($firstItem)->toHaveKey('totalCost')
            ->and($firstItem)->toHaveKey('remainingAmount');
    }
});

it('creates a medical record', function () {
    $patient = Patient::factory()->create();

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Sample case',
        'description' => 'Sample description',
        'totalCost' => 1000.50,
        'remainingAmount' => 500.25,
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('medical_records', ['id' => $id]);
});

it('shows a medical record', function () {
    $medicalRecord = MedicalRecord::factory()->create();

    $response = $this->getJson("/api/medical-records/{$medicalRecord->id}");

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data)->toBeArray();

    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('patientId')
        ->and($data)->toHaveKey('recordDate')
        ->and($data)->toHaveKey('recordType')
        ->and($data)->toHaveKey('caseName')
        ->and($data)->toHaveKey('description')
        ->and($data)->toHaveKey('totalCost')
        ->and($data)->toHaveKey('remainingAmount');
});

it('updates a medical record', function () {
    $medicalRecord = MedicalRecord::factory()->create();

    $updatePayload = [
        'recordDate' => '2025-02-02',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Updated case name',
        'description' => 'Updated description',
        'totalCost' => 1500.00,
        'remainingAmount' => 200.00,
    ];

    $response = $this->putJson("/api/medical-records/{$medicalRecord->id}", $updatePayload);

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a medical record', function () {
    $medicalRecord = MedicalRecord::factory()->create();

    $response = $this->deleteJson("/api/medical-records/{$medicalRecord->id}");

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('medical_records', ['id' => $medicalRecord->id]);
});

it('returns 404 when showing non-existent medical record', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->getJson('/api/medical-records/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when updating non-existent medical record', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $payload = [
        'recordDate' => '2025-02-02',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Updated case',
    ];

    $response = $this->putJson('/api/medical-records/'.$nonExistentId, $payload);

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent medical record', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->deleteJson('/api/medical-records/'.$nonExistentId);

    $response->assertNotFound();
});

