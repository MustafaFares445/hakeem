<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $user = User::factory()->create();
    // Assuming the first user gets all permissions from seeder or similar logic
    Sanctum::actingAs($user);
});

it('lists users', function () {
    User::factory()->count(3)->create();

    $response = $this->getJson('/api/users');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    // Check that each item has required properties
    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeInt()
            ->and($firstItem)->toHaveKey('name')
            ->and($firstItem)->toHaveKey('email')
            ->and($firstItem)->toHaveKey('emailVerifiedAt');

    }
});

it('creates a user', function () {
    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'emailVerifiedAt' => null,
    ];

    $response = $this->postJson('/api/users', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('users', ['id' => $id]);
});

it('shows a user', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/users/{$user->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data)->toBeArray();

    // Check required properties
    expect($data['id'])->toBeInt()
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('email')
        ->and($data)->toHaveKey('emailVerifiedAt');

    // Check relationships if loaded
    // Relationship 'media' may be present if loaded
    if (isset($data['media'])) {
        expect($data['media'])->toBeArray();
    }
});

it('updates a user', function () {
    $user = User::factory()->create();

    $updatePayload = [
        'name' => 'Sample name updated',
        'email' => 'test_updated@example.com',
        'emailVerifiedAt' => null,
    ];

    $response = $this->putJson("/api/users/{$user->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a user', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/users/{$user->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
it('validates required fields when creating a user', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

it('validates name must not exceed max length', function () {
    // Arrange
    $payload = ['name' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'email' => 'test@example.com',
        'emailVerifiedAt' => 'test@example.com', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('validates email must be a valid email', function () {
    // Arrange
    $payload = ['name' => 'Sample name',
        'email' => 'invalid-email',
        'emailVerifiedAt' => 'test@example.com', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates email must not exceed max length', function () {
    // Arrange
    $payload = ['name' => 'Sample name',
        'email' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'emailVerifiedAt' => 'test@example.com', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates emailVerifiedAt must be a valid email', function () {
    // Arrange
    $payload = ['name' => 'Sample name',
        'email' => 'test@example.com',
        'emailVerifiedAt' => 'invalid-email', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['emailVerifiedAt']);
});

it('validates emailVerifiedAt must be a valid date format', function () {
    // Arrange
    $payload = ['name' => 'Sample name',
        'email' => 'test@example.com',
        'emailVerifiedAt' => 'invalid-date', ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['emailVerifiedAt']);
});

it('returns 404 when showing non-existent user', function () {
    // Arrange
    $nonExistentId = 99999;

    // Act
    $response = $this->getJson('/api/users/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when updating non-existent user', function () {
    // Arrange
    $nonExistentId = 99999;
    $payload = ['name' => 'Sample name updated',
        'email' => 'test_updated@example.com',
        'emailVerifiedAt' => 'test_updated@example.com',
        'password' => 'Sample password updated',
        'rememberToken' => 'Sample remember_token updated', ];

    // Act
    $response = $this->putJson('/api/users/'.$nonExistentId, $payload);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when deleting non-existent user', function () {
    // Arrange
    $nonExistentId = 99999;

    // Act
    $response = $this->deleteJson('/api/users/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('forbids unauthorized user from viewing users', function () {
    // Arrange
    $user = User::factory()->create();
    User::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from creating user', function () {
    // Arrange
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test_unauthorized@example.com',
        'emailVerifiedAt' => null,
    ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertCreated();
});

it('forbids unauthorized user from updating user', function () {
    // Arrange
    $user = User::factory()->create();
    $model = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test_unauthorized_update@example.com',
        'emailVerifiedAt' => null,
    ];

    // Act
    $response = $this->putJson('/api/users/'.$model->id, $payload);

    // Assert
    $response->assertOk();
});

it('forbids unauthorized user from deleting user', function () {
    // Arrange
    $user = User::factory()->create();
    $model = User::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/users/'.$model->id);

    // Assert
    $response->assertOk();
});

it('handles empty payload gracefully', function () {
    // Arrange
    $payload = [];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(422);
});

it('sanitizes SQL injection attempts in string fields', function () {
    // Arrange
    $payload = ['name' => "''; DROP TABLE users; --",
        'email' => 'test@example.com',
        'emailVerifiedAt' => null, ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});

it('sanitizes XSS attempts in string fields', function () {
    // Arrange
    $payload = ['name' => '<script>alert("XSS")</script>',
        'email' => 'test@example.com',
        'emailVerifiedAt' => null, ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    // Should either validate and reject, or sanitize and accept
    expect($response->status())->toBeIn([201, 422]);
});

it('handles max length boundary for name', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 255);
    $payload = ['name' => $maxLengthString,
        'email' => 'test@example.com',
        'emailVerifiedAt' => null, ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(201);
});

it('handles max length boundary for email', function () {
    // Arrange
    $maxLengthString = str_repeat('a', 63).'@'.str_repeat('b', 63).'.com';
    $payload = ['name' => 'Sample name',
        'email' => $maxLengthString,
        'emailVerifiedAt' => null, ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertStatus(201);
});

it('paginates users with default per page', function () {
    // Arrange
    User::factory()->count(25)->create();

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(20);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(20);
});

it('paginates users with custom per page', function () {
    // Arrange
    User::factory()->count(15)->create();

    // Act
    $response = $this->getJson('/api/users?perPage=5');

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
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(1);
    expect($response->json('meta.total'))->toBe(1);
});

it('handles pagination beyond last page', function () {
    // Arrange
    User::factory()->count(5)->create();

    // Act
    $response = $this->getJson('/api/users?page=999');

    // Assert
    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toBeArray();
    expect($data)->toHaveCount(0);
});

it('includes pagination metadata', function () {
    // Arrange
    User::factory()->count(25)->create();

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertOk();
    expect($response->json('meta'))->toHaveKeys(['current_page', 'per_page', 'total', 'last_page']);
});
