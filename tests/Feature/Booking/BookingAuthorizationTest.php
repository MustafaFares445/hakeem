<?php

declare(strict_types=1);

use App\Enums\AppointmentTypeEnum;
use App\Models\Booking;
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

it('forbids unauthorized user from viewing bookings', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/bookings');

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from creating booking', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

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
    $response->assertForbidden();
});

it('forbids unauthorized user from updating booking', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = Booking::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Preview->value,
    ];

    // Act
    $response = $this->putJson('/api/bookings/'.$model->id, $payload);

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from deleting booking', function () {
    // Arrange
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    $model = Booking::factory()->create(['tenant_id' => $this->tenant->id]);
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/bookings/'.$model->id);

    // Assert
    $response->assertForbidden();
});
