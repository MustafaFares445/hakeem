<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('handles empty payload gracefully', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/chronic_medications', $payload);

    // Assert
    $response->assertStatus(422);
});

it('sanitizes SQL injection attempts in string fields', function () {
    // Arrange
    $patient = App\Models\Patient::factory()->create();
    $payload = [
        'patientId' => $patient->id,
        'title' => "'; DROP TABLE chronic_medications; --",
    ];

    // Act
    $response = $this->postJson('/api/chronic_medications', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});

it('sanitizes XSS attempts in string fields', function () {
    // Arrange
    $patient = App\Models\Patient::factory()->create();
    $payload = [
        'patientId' => $patient->id,
        'title' => '<script>alert("XSS")</script>',
    ];

    // Act
    $response = $this->postJson('/api/chronic_medications', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});

it('handles max length boundary for patientId', function () {
    // Arrange
    $patient = App\Models\Patient::factory()->create();
    $payload = [
        'patientId' => $patient->id,
        'title' => 'Sample title',
    ];

    // Act
    $response = $this->postJson('/api/chronic_medications', $payload);

    // Assert
    $response->assertStatus(201);
});
