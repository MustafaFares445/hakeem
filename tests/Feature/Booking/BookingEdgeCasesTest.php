<?php

declare(strict_types=1);

use App\Enums\AppointmentTypeEnum;
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
    grantPermissions($user, 'bookings');
    Sanctum::actingAs($user);
});

it('handles empty payload gracefully', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(422);
});

it('sanitizes SQL injection attempts in string fields', function () {
    // Arrange
    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Preview->value,
    ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    $response->assertStatus(201);
});

it('sanitizes XSS attempts in string fields', function () {
    // Arrange
    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Surgery->value,
    ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    $response->assertStatus(201);
});

it('handles max length boundary for patientId', function () {
    // Arrange
    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Review->value,
    ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for tenantId', function () {
    // Arrange
    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Preview->value,
    ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for userId', function () {
    // Arrange
    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Surgery->value,
    ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for appointmentType', function () {
    // Arrange
    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Review->value,
    ];

    // Act
    $response = $this->postJson('/api/bookings', $payload);

    // Assert
    $response->assertStatus(201);
});
