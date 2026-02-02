<?php

declare(strict_types=1);

use App\Enums\PatientGenderEnum;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing patients', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/patients');

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from creating patient', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes',
    ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from updating patient', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes',
    ];

    // Act
    $response = $this->putJson('/api/patients/'.$model->id, $payload);

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from deleting patient', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/patients/'.$model->id);

    // Assert
    $response->assertForbidden();
});
