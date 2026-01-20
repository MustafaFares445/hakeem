<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('bulk creates users', function () {
    $payload = [
        'items' => [
            [
                'name' => 'Sample name',
                'email' => 'test1@example.com',
                'emailVerifiedAt' => null,
            ],
            [
                'name' => 'Sample name',
                'email' => 'test2@example.com',
                'emailVerifiedAt' => null,
            ],
        ],
    ];

    $response = $this->postJson('/api/users/bulk', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());

    $createdItems = $response->json('data');
    expect($createdItems)->toBeArray();
    expect(count($createdItems))->toBe(2);
});

it('validates maximum bulk size on store', function () {
    $payload = [
        'items' => array_fill(0, 101, [
            'name' => 'Sample name',
            'email' => 'test@example.com',
            'emailVerifiedAt' => null,
        ]),
    ];

    $response = $this->postJson('/api/users/bulk', $payload);
    $response->assertStatus(422);
});

it('validates duplicate unique keys on store', function () {
    $payload = [
        'items' => [
            [
                'name' => 'Sample name',
                'email' => 'dup@example.com',
                'emailVerifiedAt' => null,
            ],
            [
                'name' => 'Sample name',
                'email' => 'dup@example.com',
                'emailVerifiedAt' => null,
            ],
        ],
    ];

    $response = $this->postJson('/api/users/bulk', $payload);
    $response->assertStatus(422);
});

it('bulk updates users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $payload = [
        'items' => [
            [
                'id' => $user1->id,
                'name' => 'Sample name updated',
                'email' => 'test_updated1@example.com',
                'emailVerifiedAt' => null,
            ],
            [
                'id' => $user2->id,
                'name' => 'Sample name updated',
                'email' => 'test_updated2@example.com',
                'emailVerifiedAt' => null,
            ],
        ],
    ];

    $response = $this->putJson('/api/users/bulk', $payload);
    $response->assertOk()->assertJsonPath('message', ResponseMessages::UPDATED->message());

    $updatedItems = $response->json('data');
    expect($updatedItems)->toBeArray();
    expect(count($updatedItems))->toBe(2);
});

it('validates missing model IDs on update', function () {
    $payload = [
        'items' => [
            [
                'id' => 9999,
                'name' => 'Sample name updated',
                'email' => 'test_updated@example.com',
                'emailVerifiedAt' => null,
            ],
        ],
    ];

    $response = $this->putJson('/api/users/bulk', $payload);
    $response->assertStatus(422);
});

it('validates duplicate unique keys on update', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $payload = [
        'items' => [
            [
                'id' => $user1->id,
                'name' => 'Sample name updated',
                'email' => 'dup_update@example.com',
                'emailVerifiedAt' => null,
            ],
            [
                'id' => $user2->id,
                'name' => 'Sample name updated',
                'email' => 'dup_update@example.com',
                'emailVerifiedAt' => null,
            ],
        ],
    ];

    $response = $this->putJson('/api/users/bulk', $payload);
    $response->assertStatus(422);
});

it('bulk deletes users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $payload = [
        'ids' => [$user1->id, $user2->id],
    ];

    $response = $this->deleteJson('/api/users/bulk', $payload);
    $response->assertOk()->assertJsonPath('message', ResponseMessages::DELETED->message());
    $response->assertJsonPath('deleted_count', 2);

    $this->assertDatabaseMissing('users', ['id' => $user1->id]);
    $this->assertDatabaseMissing('users', ['id' => $user2->id]);
});

it('validates missing model IDs on delete', function () {
    $payload = [
        'ids' => [9999],
    ];

    $response = $this->deleteJson('/api/users/bulk', $payload);
    $response->assertStatus(422);
});
