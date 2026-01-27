<?php

declare(strict_types=1);

use App\Enums\RecordTypeEnum;
use App\Models\MedicalRecord;
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

it('sorts medical records by record date', function () {
    $patient = Patient::factory()->create();
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'record_date' => '2025-01-01']);
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'record_date' => '2025-02-01']);

    $response = $this->getJson('/api/medical-records?sort=recordDate');
    $response->assertOk();

    $first = $response->json('data.0.recordDate');
    expect((string) $first)->toBe('2025-01-01');

    $response = $this->getJson('/api/medical-records?sort=-recordDate');
    $response->assertOk();

    $firstDesc = $response->json('data.0.recordDate');
    expect((string) $firstDesc)->toBe('2025-02-01');
});

it('filters medical records by patientId', function () {
    $patient1 = Patient::factory()->create();
    $patient2 = Patient::factory()->create();

    MedicalRecord::factory()->create(['patient_id' => $patient1->id]);
    MedicalRecord::factory()->create(['patient_id' => $patient2->id]);

    $response = $this->getJson('/api/medical-records?filter[patientId]='.$patient1->id);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters medical records by recordType', function () {
    $patient = Patient::factory()->create();
    $typeA = RecordTypeEnum::cases()[0]->value;
    $typeB = RecordTypeEnum::cases()[1]->value ?? $typeA;

    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'record_type' => $typeA]);
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'record_type' => $typeB]);

    $response = $this->getJson('/api/medical-records?filter[recordType]='.$typeA);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters medical records by caseName', function () {
    $patient = Patient::factory()->create();
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'case_name' => 'Target case']);
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'case_name' => 'Other case']);

    $response = $this->getJson('/api/medical-records?filter[caseName]='.urlencode('Target case'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters medical records by description', function () {
    $patient = Patient::factory()->create();
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'description' => 'Special description']);
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'description' => 'Other description']);

    $response = $this->getJson('/api/medical-records?filter[description]='.urlencode('Special description'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters medical records by record date range', function () {
    $patient = Patient::factory()->create();
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'record_date' => '2025-01-01']);
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'record_date' => '2025-03-01']);

    $after = '2025-02-01';
    $before = '2025-04-01';

    $response = $this->getJson('/api/medical-records?filter[recordDateAfter]='.$after.'&filter[recordDateBefore]='.$before);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters medical records by created date range', function () {
    $patient = Patient::factory()->create();
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'created_at' => now()->subDays(5)]);
    MedicalRecord::factory()->create(['patient_id' => $patient->id, 'created_at' => now()]);

    $after = now()->subDays(2)->format('Y-m-d');
    $before = now()->format('Y-m-d');

    $response = $this->getJson('/api/medical-records?filter[createdAfter]='.$after.'&filter[createdBefore]='.$before);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('paginates filtered medical records', function () {
    $patient = Patient::factory()->create();
    MedicalRecord::factory()->count(15)->create(['patient_id' => $patient->id]);

    $response = $this->getJson('/api/medical-records?perPage=5&page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});

