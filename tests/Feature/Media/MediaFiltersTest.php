<?php

declare(strict_types=1);

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

beforeEach(/**
 * @throws TenantCouldNotBeIdentifiedById
 */ function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $tenant = Tenant::factory()->create();
    tenancy()->initialize($tenant);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    grantPermissions($user, 'media');
    grantPermissions($user, 'patients');
    grantPermissions($user, 'medical_records');
    Sanctum::actingAs($user);
});

it('returns patient and medical record media when filtering by patientAndMedicalRecords', function () {
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $medicalRecord = MedicalRecord::factory()->create(['patient_id' => $patient->id]);

    $patient->addMedia(UploadedFile::fake()->create('patient-doc.pdf', 100))
        ->toMediaCollection('documents');
    $medicalRecord->addMedia(UploadedFile::fake()->create('record-doc.pdf', 100))
        ->toMediaCollection('documents');
    $otherPatient->addMedia(UploadedFile::fake()->create('other-doc.pdf', 100))
        ->toMediaCollection('documents');

    $response = $this->getJson('/api/media?filter[patientAndMedicalRecords]='.$patient->id);

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(2);
});

it('requires patientAndMedicalRecords filter for index', function () {
    $response = $this->getJson('/api/media');

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['filter.patientAndMedicalRecords']);
});

it('filters media by collectionName', function () {
    $patient = Patient::factory()->create();
    $patient->addMedia(UploadedFile::fake()->create('lab.pdf', 100))
        ->toMediaCollection('laboratory-tests');
    $patient->addMedia(UploadedFile::fake()->create('xray.pdf', 100))
        ->toMediaCollection('imaging-scans');

    $response = $this->getJson('/api/media?filter[patientAndMedicalRecords]='.$patient->id.'&filter[collectionName]=laboratory-tests');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['collection'])->toBe('laboratory-tests');
});

it('paginates media with patientAndMedicalRecords filter', function () {
    $patient = Patient::factory()->create();
    for ($i = 0; $i < 15; $i++) {
        $patient->addMedia(UploadedFile::fake()->create("doc-{$i}.pdf", 100))
            ->toMediaCollection('documents');
    }

    $response = $this->getJson('/api/media?filter[patientAndMedicalRecords]='.$patient->id.'&perPage=5&page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});
