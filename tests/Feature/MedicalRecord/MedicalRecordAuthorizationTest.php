<?php

declare(strict_types=1);

use App\Enums\RecordTypeEnum;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing medical records', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    MedicalRecord::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $patient->id]);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/medical-records');

    $response->assertForbidden();
});

it('forbids unauthorized user from creating medical records', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);

    $payload = [
        'patientId' => $patient->id,
        'recordDate' => '2025-01-01',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Sample case',
    ];

    $response = $this->postJson('/api/medical-records', $payload);

    $response->assertForbidden();
});

it('forbids unauthorized user from updating medical records', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = MedicalRecord::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $patient->id]);
    Sanctum::actingAs($user);

    $payload = [
        'recordDate' => '2025-02-02',
        'recordType' => RecordTypeEnum::cases()[0]->value,
        'caseName' => 'Updated case',
    ];

    $response = $this->putJson('/api/medical-records/'.$model->id, $payload);

    $response->assertForbidden();
});

it('forbids unauthorized user from deleting medical records', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = MedicalRecord::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $patient->id]);
    Sanctum::actingAs($user);

    $response = $this->deleteJson('/api/medical-records/'.$model->id);

    $response->assertForbidden();
});
