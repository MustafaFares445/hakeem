<?php

declare(strict_types=1);

use App\Enums\PatientGenderEnum;
use App\Models\Patient;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $user = User::factory()->create();
    // Assuming the first user gets all permissions from seeder or similar logic
    Sanctum::actingAs($user);
});

it('lists patients', function () {
    Patient::factory()->count(3)->create();

    $response = $this->getJson('/api/patients');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    // Check that each item has required properties
    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('name')
            ->and($firstItem)->toHaveKey('email')
            ->and($firstItem)->toHaveKey('phoneNumber')
            ->and($firstItem)->toHaveKey('birthday')
            ->and($firstItem)->toHaveKey('gender')
            ->and($firstItem)->toHaveKey('city')
            ->and($firstItem)->toHaveKey('streetAddress')
            ->and($firstItem)->toHaveKey('registrationDate')
            ->and($firstItem)->toHaveKey('notes');

    }
});

it('creates a patient', function () {
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

    $response = $this->postJson('/api/patients', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('patients', ['id' => $id]);
});

it('shows a patient', function () {
    $patient = Patient::factory()->create();

    $response = $this->getJson("/api/patients/{$patient->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data)->toBeArray();

    // Check required properties
    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('email')
        ->and($data)->toHaveKey('phoneNumber')
        ->and($data)->toHaveKey('birthday')
        ->and($data)->toHaveKey('gender')
        ->and($data)->toHaveKey('city')
        ->and($data)->toHaveKey('streetAddress')
        ->and($data)->toHaveKey('registrationDate')
        ->and($data)->toHaveKey('notes');

    // Check relationships if loaded

});

it('updates a patient', function () {
    $patient = Patient::factory()->create();

    $updatePayload = [
        'name' => 'Sample name updated',
        'email' => 'test_updated@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city updated',
        'streetAddress' => 'Sample street_address updated',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes updated',
    ];

    $response = $this->putJson("/api/patients/{$patient->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a patient', function () {
    $patient = Patient::factory()->create();

    $response = $this->deleteJson("/api/patients/{$patient->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
});
it('returns 404 when showing non-existent patient', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->getJson('/api/patients/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when updating non-existent patient', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';
    $payload = [
        'name' => 'Sample name updated',
        'email' => 'test_updated@example.com',
        'phoneNumber' => '+1234567890',
        'birthday' => '2025-01-01',
        'gender' => PatientGenderEnum::cases()[0]->value,
        'city' => 'Sample city updated',
        'streetAddress' => 'Sample street_address updated',
        'registrationDate' => '2025-01-01',
        'notes' => 'Sample notes updated', ];

    // Act
    $response = $this->putJson('/api/patients/'.$nonExistentId, $payload);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when deleting non-existent patient', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->deleteJson('/api/patients/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});
