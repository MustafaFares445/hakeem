<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing users', function () {
    // Arrange
    $user = User::factory()->create();
    User::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from creating user', function () {
    // Arrange
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
    ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from updating user', function () {
    // Arrange
    $user = User::factory()->create();
    $model = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
    ];

    // Act
    $response = $this->putJson('/api/users/'.$model->id, $payload);

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from deleting user', function () {
    // Arrange
    $user = User::factory()->create();
    $model = User::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/users/'.$model->id);

    // Assert
    $response->assertForbidden();
});
