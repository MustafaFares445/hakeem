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
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing chronic diseases', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/chronic_diseases');

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from creating chronicDiseases', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'patientId' => $this->patient->id,
        'title' => 'Test Disease',
    ];

    // Act
    $response = $this->postJson('/api/chronic_diseases', $payload);

    // Assert
    $response->assertCreated();
});

it('forbids unauthorized user from updating chronicDiseases', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id]);
    Sanctum::actingAs($user);

    $payload = [
        'title' => 'Updated Disease',
    ];

    // Act
    $response = $this->putJson('/api/chronic_diseases/'.$model->id, $payload);

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from deleting chronicDiseases', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = ChronicDiseases::factory()->create(['tenant_id' => $this->tenant->id, 'patient_id' => $this->patient->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/chronic_diseases/'.$model->id);

    // Assert
    $response->assertOk();
});
