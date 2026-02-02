<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'bookings');
    Sanctum::actingAs($user);
});

it('validates required fields when creating a booking', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['date', 'time', 'appointmentType']);
});

it('validates patientId must not exceed max length', function () {
    // Arrange
    $payload = ['patientId' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type', ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId']);
});

it('validates tenantId must not exceed max length', function () {
    // Arrange
    $payload = ['patientId' => null,
        'tenantId' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type', ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['tenantId']);
});

it('validates userId must not exceed max length', function () {
    // Arrange
    $payload = ['patientId' => null,
        'tenantId' => null,
        'userId' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type', ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['userId']);
});

it('validates date must be a valid date format', function () {
    // Arrange
    $payload = ['patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => 'invalid-date',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type', ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['date']);
});

it('validates appointmentType must not exceed max length', function () {
    // Arrange
    $payload = ['patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['appointmentType']);
});
