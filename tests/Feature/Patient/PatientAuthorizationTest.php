<?php

declare(strict_types=1);

use App\Enums\PatientGenderEnum;
use App\Models\Patient;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing patients', function () {
    // Arrange
    $user = User::factory()->create();
    Patient::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/patients');

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from creating patient', function () {
    // Arrange
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes',
    ];

    // Act
    $response = $this->postJson('/api/patients', $payload);

    // Assert
    $response->assertCreated();
});

it('forbids unauthorized user from updating patient', function () {
    // Arrange
    $user = User::factory()->create();
    $model = Patient::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city',
        'streetAddress' => 'Sample street_address',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes',
    ];

    // Act
    $response = $this->putJson('/api/patients/'.$model->id, $payload);

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from deleting patient', function () {
    // Arrange
    $user = User::factory()->create();
    $model = Patient::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/patients/'.$model->id);

    // Assert
    $response->assertOk();
});
