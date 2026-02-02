<?php

declare(strict_types=1);

use App\Models\ChronicDiseases;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $this->patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'chronic_diseases');
    Sanctum::actingAs($user);
});

it('filters chronic diseases by search term', function () {
    ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id, 'title' => 'Target chronicDiseases']);
    ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id, 'title' => 'Other chronicDiseases']);

    $response = $this->withHeader('Accept-Language', 'en')
        ->getJson('/api/chronic_diseases?search=Target');

    $response->assertOk();
    // Adjusted check for translatable or normal string
    $data = $response->json('data');
    $found = false;
    foreach ($data as $item) {
        $val = is_array($item['title']) ? json_encode($item['title']) : (string) $item['title'];
        if (str_contains($val, 'Target')) {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();
});

it('sorts chronic diseases', function () {
    ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id, 'title' => 'A chronicDiseases']);
    ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id, 'title' => 'Z chronicDiseases']);

    $response = $this->getJson('/api/chronic_diseases?sort=title');
    $response->assertOk();

    $val1 = $response->json('data.0.title');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/chronic_diseases?sort=-title');
    $response->assertOk();

    $val2 = $response->json('data.0.title');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});

it('filters chronic diseases by date range', function () {
    ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id, 'created_at' => now()->subDays(5)]);
    ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id, 'created_at' => now()]);

    $after = now()->subDays(2)->format('Y-m-d');
    $before = now()->format('Y-m-d');

    $response = $this->getJson('/api/chronic_diseases?filter[createdAfter]='.$after.'&filter[createdBefore]='.$before);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('paginates filtered chronic diseases', function () {
    ChronicDiseases::factory()->count(15)->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id]);

    $response = $this->getJson('/api/chronic_diseases?perPage=5&page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});
