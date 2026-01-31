<?php

declare(strict_types=1);

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

beforeEach(/**
 * @throws TenantCouldNotBeIdentifiedById
 */ function () {
    Storage::fake('public');
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $tenant = Tenant::factory()->create();
    tenancy()->initialize($tenant);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    grantPermissions($user, 'media');
    grantPermissions($user, 'patients');
    grantPermissions($user, 'medical_records');
    Sanctum::actingAs($user);
});

it('lists media for patient and their medical records', function () {
    $patient = Patient::factory()->create();
    $patient->addMedia(UploadedFile::fake()->create('doc.pdf', 100))
        ->toMediaCollection('documents');

    $response = $this->getJson('/api/media?filter[patientAndMedicalRecords]='.$patient->id);

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();
});

it('creates media for patient', function () {
    $patient = Patient::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->post('/api/media', [
        'patientId' => $patient->id,
        'files' => [$file],
    ]);

    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    expect($response->json('data'))->toBeArray();
    $this->assertDatabaseHas('media', ['model_type' => Patient::class, 'model_id' => $patient->id]);
});

it('creates media for medical record', function () {
    $patient = Patient::factory()->create();
    $medicalRecord = MedicalRecord::factory()->create(['patient_id' => $patient->id]);
    $file = UploadedFile::fake()->create('attachment.pdf', 100);

    $response = $this->post('/api/media', [
        'patientId' => $patient->id,
        'medicalRecordId' => $medicalRecord->id,
        'files' => [$file],
    ]);

    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $this->assertDatabaseHas('media', ['model_type' => MedicalRecord::class, 'model_id' => $medicalRecord->id]);
});

it('shows a media item', function () {
    $tenant = tenant();
    $patient = Patient::factory()->create(['tenant_id' => $tenant->id]);
    $media = $patient->addMedia(UploadedFile::fake()->create('doc.pdf', 100))
        ->toMediaCollection('documents');

    $response = $this->getJson('/api/media/'.$media->id);

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    $data = $response->json('data') ?? $response->json('0.data');
    expect($data)->toHaveKey('id')
        ->and($data['id'])->toBe($media->id);
});

it('deletes a media item', function () {
    $tenant = tenant();
    $patient = Patient::factory()->create(['tenant_id' => $tenant->id]);
    $media = $patient->addMedia(UploadedFile::fake()->create('doc.pdf', 100))
        ->toMediaCollection('documents');
    $mediaId = $media->id;

    $response = $this->deleteJson('/api/media/'.$mediaId);

    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());
});

it('returns 404 when showing non-existent media', function () {
    $response = $this->getJson('/api/media/999999999');

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent media', function () {
    $response = $this->deleteJson('/api/media/999999999');

    $response->assertNotFound();
});
