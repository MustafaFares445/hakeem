<?php

declare(strict_types=1);

use App\Models\ChronicDiseases;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

beforeEach(/**
 * @throws JsonException
 * @throws TenantCouldNotBeIdentifiedById
 */ function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $tenant = Tenant::factory()->create();
    tenancy()->initialize($tenant);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    grantPermissions($user, 'chronic_diseases');
    Sanctum::actingAs($user);
});

it('lists chronic diseases', function () {
    ChronicDiseases::factory()->count(3)->create();

    $response = $this->getJson('/api/chronic_diseases');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    // Check that each item has required properties
    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];

    }
});

it('creates a chronicDiseases', function () {
    $patient = App\Models\Patient::factory()->create();
    $payload = [
        'patientId' => $patient->id,
        'title' => 'Test Disease',
    ];

    $response = $this->postJson('/api/chronic_diseases', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('chronic_diseases', ['id' => $id]);
});

it('shows a chronicDiseases', function () {
    $chronicDiseases = ChronicDiseases::factory()->create();

    $response = $this->getJson("/api/chronic_diseases/{$chronicDiseases->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data)->toBeArray();

    // Check required properties

    // Check relationships if loaded

});

it('updates a chronicDiseases', function () {
    $chronicDiseases = ChronicDiseases::factory()->create();

    $updatePayload = [
        'title' => 'Updated Disease',
    ];

    $response = $this->putJson("/api/chronic_diseases/{$chronicDiseases->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a chronicDiseases', function () {
    $chronicDiseases = ChronicDiseases::factory()->create();

    $response = $this->deleteJson("/api/chronic_diseases/{$chronicDiseases->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('chronic_diseases', ['id' => $chronicDiseases->id]);
});
it('returns 404 when showing non-existent chronicDiseases', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->getJson('/api/chronic_diseases/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when updating non-existent chronicDiseases', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';
    $payload = [];

    // Act
    $response = $this->putJson('/api/chronic_diseases/'.$nonExistentId, $payload);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when deleting non-existent chronicDiseases', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->deleteJson('/api/chronic_diseases/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when ID format is invalid', function () {
    // Arrange
    $invalidId = 'invalid-uuid';

    // Act
    $response = $this->getJson('/api/chronic_diseases/'.$invalidId);

    // Assert
    $response->assertNotFound();
});
