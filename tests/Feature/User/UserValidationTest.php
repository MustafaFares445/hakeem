<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    grantUserPermissions($user);
    Sanctum::actingAs($user);
});

it('validates required fields when creating a user', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'username', 'email']);
});

it('validates name must not exceed max length', function () {
    // Arrange
    $payload = ['name' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'username' => 'testuser',
        'email' => 'test@example.com', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('validates email must be a valid email', function () {
    // Arrange
    $payload = ['name' => 'Sample name',
        'username' => 'testuser',
        'email' => 'invalid-email', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates email must not exceed max length', function () {
    // Arrange
    $payload = ['name' => 'Sample name',
        'username' => 'testuser',
        'email' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

