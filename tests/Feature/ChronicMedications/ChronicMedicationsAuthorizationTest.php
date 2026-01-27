<?php

declare(strict_types=1);

use App\Models\ChronicMedications;
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
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing chronic medications', function () {
    // Arrange
    $user = User::factory()->create();
    ChronicMedications::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/chronic_medications');

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from creating chronicMedications', function () {
    // Arrange
    $user = User::factory()->create();
    $patient = App\Models\Patient::factory()->create();
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
    $user = User::factory()->create();
    $model = ChronicMedications::factory()->create();
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
    $user = User::factory()->create();
    $model = ChronicMedications::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/chronic_medications/'.$model->id);

    // Assert
    $response->assertOk();
});
