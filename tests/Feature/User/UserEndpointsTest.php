<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantUserPermissions($user);
    Sanctum::actingAs($user);
});

it('lists users', function () {
    User::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/users');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('name')
            ->and($firstItem)->toHaveKey('email');
    }
});

it('creates a user', function () {
    $payload = [
        'name' => 'Sample name',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password',
        'roles' => [RoleEnum::cases()[0]->value],
    ];

    $response = $this->postJson('/api/users', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('users', ['id' => $id]);
});

it('shows a user', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson("/api/users/{$user->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');

    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('email');
    // Relationship 'media' may be present if loaded
    if (isset($data['media'])) {
        expect($data['media'])->toBeArray();
    }
});

it('updates a user', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

    $updatePayload = [
        'name' => 'Sample name updated',
        'username' => 'testuser_updated',
        'email' => 'test_updated@example.com',
        'password' => 'password',
    ];

    $response = $this->putJson("/api/users/{$user->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a user', function () {
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->deleteJson("/api/users/{$user->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
it('returns 404 when showing non-existent user', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->getJson('/api/users/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when updating non-existent user', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';
    $payload = [
        'name' => 'Sample name updated',
        'username' => 'testuser_updated',
        'email' => 'test_updated@example.com',
        'password' => 'password',
    ];

    // Act
    $response = $this->putJson('/api/users/'.$nonExistentId, $payload);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when deleting non-existent user', function () {
    // Arrange
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    // Act
    $response = $this->deleteJson('/api/users/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when ID format is invalid', function () {
    // Arrange
    $invalidId = 'invalid-uuid';

    // Act
    $response = $this->getJson('/api/users/'.$invalidId);

    // Assert
    $response->assertNotFound();
});
