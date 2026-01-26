<?php

declare(strict_types=1);

use App\Models\ChronicDiseases;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing chronic diseases', function () {
    // Arrange
    $user = User::factory()->create();
    ChronicDiseases::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/chronic_diseases');

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from creating chronicDiseases', function () {
    // Arrange
    $user = User::factory()->create();
    $patient = App\Models\Patient::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'patientId' => $patient->id,
        'title' => 'Test Disease',
    ];

    // Act
    $response = $this->postJson('/api/chronic_diseases', $payload);

    // Assert
    $response->assertCreated();
});

it('forbids unauthorized user from updating chronicDiseases', function () {
    // Arrange
    $user = User::factory()->create();
    $model = ChronicDiseases::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'title' => 'Updated Disease',
    ];

    // Act
    $response = $this->putJson('/api/chronic_diseases/'.$model->id, $payload);

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from deleting chronicDiseases', function () {
    // Arrange
    $user = User::factory()->create();
    $model = ChronicDiseases::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/chronic_diseases/'.$model->id);

    // Assert
    $response->assertOk();
});
