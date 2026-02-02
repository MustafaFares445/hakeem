<?php

declare(strict_types=1);

use App\Models\ChronicMedications;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'chronic_medications');
    Sanctum::actingAs($user);
});

it('sorts chronic medications', function () {
    ChronicMedications::factory()->create(['title' => 'A chronicMedications', 'tenant_id' => $this->tenant->id]);
    ChronicMedications::factory()->create(['title' => 'Z chronicMedications', 'tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/chronic_medications?sort=title');
    $response->assertOk();

    $val1 = $response->json('data.0.title');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/chronic_medications?sort=-title');
    $response->assertOk();

    $val2 = $response->json('data.0.title');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});

it('filters chronic medications by patient_id', function () {
    $patient = App\Models\Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    ChronicMedications::factory()->create(['patient_id' => $patient->id, 'tenant_id' => $this->tenant->id]);
    ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/chronic_medications?filter[patientId]='.$patient->id);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters chronic medications by title', function () {
    ChronicMedications::factory()->create(['title' => 'Sample title', 'tenant_id' => $this->tenant->id]);
    ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/chronic_medications?filter[title]='.('Sample title'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters chronic medications by date range', function () {
    ChronicMedications::factory()->create(['created_at' => now()->subDays(5), 'tenant_id' => $this->tenant->id]);
    ChronicMedications::factory()->create(['created_at' => now(), 'tenant_id' => $this->tenant->id]);

    $after = now()->subDays(2)->format('Y-m-d');
    $before = now()->format('Y-m-d');

    $response = $this->getJson('/api/chronic_medications?filter[createdAfter]='.$after.'&filter[createdBefore]='.$before);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('paginates filtered chronic medications', function () {
    ChronicMedications::factory()->count(15)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/chronic_medications?perPage=5&page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});
