<?php

declare(strict_types=1);

use App\Models\ChronicMedications;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'chronic_medications');
    Sanctum::actingAs($user);
});

it('lists chronic medications', function () {
    ChronicMedications::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/chronic_medications');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    // Check that each item has required properties
    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('patientId')
            ->and($firstItem)->toHaveKey('title');

    }
});

it('creates a chronicMedications', function () {
    $patient = App\Models\Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    $payload = [
        'patientId' => $patient->id,
        'title' => 'Sample title',
    ];

    $response = $this->postJson('/api/chronic_medications', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('chronic_medications', ['id' => $id]);
});

it('shows a chronicMedications', function () {
    $chronicMedications = ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson("/api/chronic_medications/{$chronicMedications->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data)->toBeArray();

    // Check required properties
    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('patientId')
        ->and($data)->toHaveKey('title');

    // Check relationships if loaded

});

it('updates a chronicMedications', function () {
    $chronicMedications = ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);

    $updatePayload = [
        'title' => 'Sample title updated',
    ];

    $response = $this->putJson("/api/chronic_medications/{$chronicMedications->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a chronicMedications', function () {
    $chronicMedications = ChronicMedications::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->deleteJson("/api/chronic_medications/{$chronicMedications->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('chronic_medications', ['id' => $chronicMedications->id]);
});
it('returns 404 when showing non-existent chronicMedications', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->getJson('/api/chronic_medications/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when updating non-existent chronicMedications', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';
    $payload = [
        'title' => 'Sample title updated',
    ];

    // Act
    $response = $this->putJson('/api/chronic_medications/'.$nonExistentId, $payload);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when deleting non-existent chronicMedications', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->deleteJson('/api/chronic_medications/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when ID format is invalid', function () {
    // Arrange
    $invalidId = 'invalid-uuid';

    // Act
    $response = $this->getJson('/api/chronic_medications/'.$invalidId);

    // Assert
    $response->assertNotFound();
});
