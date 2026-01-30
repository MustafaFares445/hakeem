<?php

declare(strict_types=1);

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
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from listing media', function () {
    $patient = Patient::factory()->create();

    $response = $this->getJson('/api/media?filter[patientAndMedicalRecords]='.$patient->id);

    $response->assertForbidden();
});

it('forbids unauthorized user from creating media', function () {
    $patient = Patient::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->post('/api/media', [
        'patientId' => $patient->id,
        'files' => [$file],
    ]);

    $response->assertForbidden();
});

it('forbids unauthorized user from showing media', function () {
    $patient = Patient::factory()->create();
    $media = $patient->addMedia(UploadedFile::fake()->create('doc.pdf', 100))
        ->toMediaCollection('documents');

    $unauthorizedUser = User::factory()->create(['tenant_id' => tenant('id')]);
    Sanctum::actingAs($unauthorizedUser);

    $response = $this->getJson('/api/media/'.$media->id);

    $response->assertForbidden();
});

it('forbids unauthorized user from deleting media', function () {
    $patient = Patient::factory()->create();
    $media = $patient->addMedia(UploadedFile::fake()->create('doc.pdf', 100))
        ->toMediaCollection('documents');

    $unauthorizedUser = User::factory()->create(['tenant_id' => tenant('id')]);
    Sanctum::actingAs($unauthorizedUser);

    $response = $this->deleteJson('/api/media/'.$media->id);

    $response->assertForbidden();
});
