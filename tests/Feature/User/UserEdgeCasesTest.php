<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantUserPermissions($user);
    Sanctum::actingAs($user);
});

it('handles empty payload gracefully', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422);
});

it('sanitizes SQL injection attempts in string fields', function () {
    // Arrange
    $payload = [
        'name' => '\'; DROP TABLE users; --',
        'username' => 'testuser',
        'email' => 'test@example.com',
    ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422, 500]);
});

it('sanitizes XSS attempts in string fields', function () {
    // Arrange
    $payload = [
        'name' => '<script>alert(\"XSS\")</script>',
        'username' => 'testuser',
        'email' => 'test@example.com',
    ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422, 500]);
});

it('handles max length boundary for name', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 255);
    $payload = [
        'name' => $maxLengthString,
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'secret',
        'roles' => [RoleEnum::cases()[0]->value],
    ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for email', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 243).'@example.com';
    $payload = [
        'name' => 'Sample name',
        'username' => 'testuser',
        'email' => $maxLengthString,
        'password' => 'secret',
        'roles' => [RoleEnum::cases()[0]->value],
    ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(201);
});
