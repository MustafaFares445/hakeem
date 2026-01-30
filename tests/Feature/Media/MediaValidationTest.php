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
    Sanctum::actingAs($user);
});

it('validates required patientId and files when creating media', function () {
    $response = $this->postJson('/api/media', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId', 'files']);
});

it('validates patientId must exist', function () {
    $file = UploadedFile::fake()->create('doc.pdf', 100);

    $response = $this->postJson('/api/media', [
        'patientId' => '00000000-0000-0000-0000-000000000000',
        'files' => [$file],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId']);
});

it('validates medicalRecordId must belong to patient', function () {
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $medicalRecord = MedicalRecord::factory()->create(['patient_id' => $otherPatient->id]);
    $file = UploadedFile::fake()->create('doc.pdf', 100);

    $response = $this->post('/api/media', [
        'patientId' => $patient->id,
        'medicalRecordId' => $medicalRecord->id,
        'files' => [$file],
    ]);

    $response->assertSessionHasErrors('medicalRecordId');
});
