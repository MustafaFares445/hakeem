<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    grantUserPermissions($user);
    Sanctum::actingAs($user);
});

it('paginates users with default per page', function () {
    // Arrange
    User::factory()->count(25)->create();

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(20);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(20);
});

it('paginates users with custom per page', function () {
    // Arrange
    User::factory()->count(15)->create();

    // Act
    $response = $this->getJson('/api/users?per_page=5');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(5);
    expect($response->json('meta.per_page'))->toBe(5);
});

it('handles pagination for empty result set', function () {
    // Arrange
    // No additional models created (only the authenticated user from beforeEach)

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    // The authenticated user from beforeEach is included in results
    expect($data)->toHaveCount(1);
    expect($response->json('meta.total'))->toBe(1);
});

it('handles pagination beyond last page', function () {
    // Arrange
    User::factory()->count(5)->create();

    // Act
    $response = $this->getJson('/api/users?page=999');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
});

it('includes pagination metadata', function () {
    // Arrange
    User::factory()->count(25)->create();

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertOk();
    expect($response->json('meta'))->toHaveKeys(['current_page', 'per_page', 'total', 'last_page']);
});
