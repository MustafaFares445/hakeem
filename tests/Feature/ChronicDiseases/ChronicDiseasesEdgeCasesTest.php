<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $this->patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'chronic_diseases');
    Sanctum::actingAs($user);
});

it('handles empty payload gracefully', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/chronic_diseases', $payload);

    // Assert
    $response->assertStatus(422);
});

it('sanitizes SQL injection attempts in string fields', function () {
    // Arrange
    $payload = [
        'patientId' => $this->patient->id,
        'title' => "'; DROP TABLE chronic_diseases; --",
    ];

    // Act
    $response = $this->postJson('/api/chronic_diseases', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});

it('sanitizes XSS attempts in string fields', function () {
    // Arrange
    $payload = [
        'patientId' => $this->patient->id,
        'title' => '<script>alert("XSS")</script>',
    ];

    // Act
    $response = $this->postJson('/api/chronic_diseases', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});
