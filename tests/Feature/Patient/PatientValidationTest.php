<?php

declare(strict_types=1);

use App\Enums\PatientGenderEnum;
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
    grantPermissions($user, 'patients');
    Sanctum::actingAs($user);
});

it('validates required fields when creating a patient', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'phoneNumber', 'gender', 'registrationDate']);
});

it('validates name must not exceed max length', function () {
    // Arrange
    $payload = [
        'name' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('validates email must be a valid email', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'invalid-email',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates email must not exceed max length', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates phoneNumber must not exceed max length', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phoneNumber']);
});

it('validates birthday must be a valid date format', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => 'invalid-date',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['birthday']);
});

it('validates gender must be a valid enum value', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => 'invalid-enum-value',
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['gender']);
});

it('validates city must not exceed max length', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['city']);
});

it('validates streetAddress must not exceed max length', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['streetAddress']);
});

it('validates registrationDate must be a valid date format', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => 'invalid-date',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['registrationDate']);
});

it('validates notes must not exceed max length', function () {
    // Arrange
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['notes']);
});
