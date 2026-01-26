<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('paginates patients with default per page', function () {
    // Arrange
    Patient::factory()->count(25)->create();

    // Act
    $response = $this->getJson('/api/patients');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(20);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(20);
});

it('paginates patients with custom per page', function () {
    // Arrange
    Patient::factory()->count(15)->create();

    // Act
    $response = $this->getJson('/api/patients?per_page=5');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(5);
    expect($response->json('meta.per_page'))->toBe(5);
});

it('handles pagination for empty result set', function () {
    // Arrange
    // No models created

    // Act
    $response = $this->getJson('/api/patients');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
    expect($response->json('meta.total'))->toBe(0);
});

it('handles pagination beyond last page', function () {
    // Arrange
    Patient::factory()->count(5)->create();

    // Act
    $response = $this->getJson('/api/patients?page=999');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
});

it('includes pagination metadata', function () {
    // Arrange
    Patient::factory()->count(25)->create();

    // Act
    $response = $this->getJson('/api/patients');

    // Assert
    $response->assertOk();
    expect($response->json('meta'))->toHaveKeys(['current_page', 'per_page', 'total', 'last_page']);
});
