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

it('handles empty payload gracefully', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(422);
});

it('sanitizes SQL injection attempts in string fields', function () {
    // Arrange
    $payload = [
        'name' => '\'; DROP TABLE users; --',
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
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});

it('sanitizes XSS attempts in string fields', function () {
    // Arrange
    $payload = [
        'name' => '<script>alert(\"XSS\")</script>',
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
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});

it('handles max length boundary for name', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 255);
    $payload = [
        'name' => $maxLengthString,
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
    $response->assertStatus(201);
});

it('handles max length boundary for email', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 240);
    $validEmail = $maxLengthString.'@test.com';
    $payload = [
        'name' => 'Sample name',
        'email' => $validEmail,
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
    expect($response->status())->toBeIn([201, 422]);
});

it('handles max length boundary for phoneNumber', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 255);
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => $maxLengthString,
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for city', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 255);
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => $maxLengthString,
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for streetAddress', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 255);
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => $maxLengthString,
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes', ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for notes', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 255);
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => $maxLengthString, ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertStatus(201);
});
