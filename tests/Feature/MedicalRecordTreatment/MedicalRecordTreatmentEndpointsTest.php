<?php

declare(strict_types=1);

use App\Models\FillerMaterial;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordTreatment;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'medical_record_treatments');
    Sanctum::actingAs($user);
});

it('lists medical record treatments', function () {
    MedicalRecordTreatment::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/medical-record-treatments');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates a medical record treatment', function () {
    $medicalRecord = MedicalRecord::factory()->create(['tenant_id' => $this->tenant->id]);
    $fillerMaterial = FillerMaterial::factory()->create(['tenant_id' => $this->tenant->id]);

    $payload = [
        'medicalRecordId' => $medicalRecord->id,
        'fillerMaterialId' => $fillerMaterial->id,
        'treatmentDate' => '2025-01-01',
        'treatmentCost' => 100.0,
        'treatmentDescription' => 'Sample treatment',
        'sessionNumber' => 1,
        'doctorIds' => [User::factory()->create(['tenant_id' => $this->tenant->id])->id],
    ];

    $response = $this->postJson('/api/medical-record-treatments', $payload);

    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('medical_record_treatments', ['id' => $id]);
});

it('shows a medical record treatment', function () {
    $treatment = MedicalRecordTreatment::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson("/api/medical-record-treatments/{$treatment->id}");

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('treatmentDate')
        ->and($data)->toHaveKey('treatmentCost')
        ->and($data)->toHaveKey('treatmentDescription');
});

it('updates a medical record treatment', function () {
    $treatment = MedicalRecordTreatment::factory()->create(['tenant_id' => $this->tenant->id]);

    $updatePayload = [
        'treatmentDate' => '2025-02-02',
        'treatmentCost' => 150.0,
        'treatmentDescription' => 'Updated description',
        'sessionNumber' => 2,
    ];

    $response = $this->putJson("/api/medical-record-treatments/{$treatment->id}", $updatePayload);

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a medical record treatment', function () {
    $treatment = MedicalRecordTreatment::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->deleteJson("/api/medical-record-treatments/{$treatment->id}");

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('medical_record_treatments', ['id' => $treatment->id]);
});

it('returns 404 when showing non-existent medical record treatment', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->getJson('/api/medical-record-treatments/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when updating non-existent medical record treatment', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $payload = [
        'treatmentDate' => '2025-02-02',
    ];

    $response = $this->putJson('/api/medical-record-treatments/'.$nonExistentId, $payload);

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent medical record treatment', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->deleteJson('/api/medical-record-treatments/'.$nonExistentId);

    $response->assertNotFound();
});
