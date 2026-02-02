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
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing chronic medications', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/chronic_medications');

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from creating chronicMedications', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $patient = App\Models\Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'patientId' => $patient->id,
        'title' => 'Sample title',
    ];

    // Act
    $response = $this->postJson('/api/chronic_medications', $payload);

    // Assert
    $response->assertCreated();
});

it('forbids unauthorized user from updating chronicMedications', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'title' => 'Updated title',
    ];

    // Act
    $response = $this->putJson('/api/chronic_medications/'.$model->id, $payload);

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from deleting chronicMedications', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/chronic_medications/'.$model->id);

    // Assert
    $response->assertOk();
});
