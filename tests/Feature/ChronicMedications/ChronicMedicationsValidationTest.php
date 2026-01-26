<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('validates required fields when creating a chronicMedications', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/chronic_medications', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId', 'title']);
});

it('validates patientId must not exceed max length', function () {
    // Arrange
    $payload = ['patientId' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'title' => 'Sample title', ];

    // Act
    $response = $this->postJson('/api/chronic_medications', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patientId']);
});
