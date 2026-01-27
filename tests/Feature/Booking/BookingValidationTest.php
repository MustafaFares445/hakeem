<?php

declare(strict_types=1);

use App\Models\Booking;
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
    $payload = [        'patientId' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type',];
    
    // Act
    $response = $this->postJson('/api/bookings', $payload);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId']);
});

it('validates tenantId must not exceed max length', function () {
    // Arrange
    $payload = [        'patientId' => null,
        'tenantId' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type',];
    
    // Act
    $response = $this->postJson('/api/bookings', $payload);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['tenantId']);
});

it('validates userId must not exceed max length', function () {
    // Arrange
    $payload = [        'patientId' => null,
        'tenantId' => null,
        'userId' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type',];
    
    // Act
    $response = $this->postJson('/api/bookings', $payload);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['userId']);
});

it('validates date must be a valid date format', function () {
    // Arrange
    $payload = [        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => 'invalid-date',
        'time' => 'Sample time',
        'appointmentType' => 'Sample appointment_type',];
    
    // Act
    $response = $this->postJson('/api/bookings', $payload);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['date']);
});

it('validates appointmentType must not exceed max length', function () {
    // Arrange
    $payload = [        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',];
    
    // Act
    $response = $this->postJson('/api/bookings', $payload);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['appointmentType']);
});

