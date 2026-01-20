<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\UserBulkService;
use Illuminate\Validation\ValidationException;

it('validates maximum bulk size on store', function () {
    $service = app(UserBulkService::class);
    $dataArray = array_fill(0, 101, [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'emailVerifiedAt' => null,
    ]);

    expect(fn () => $service->store($dataArray))->toThrow(ValidationException::class);
});

it('validates duplicate email on store', function () {
    $service = app(UserBulkService::class);
    $dataArray = [
        [
            'name' => 'Sample name',
            'email' => 'test@example.com',
            'emailVerifiedAt' => null,
        ],
        [
            'name' => 'Sample name',
            'email' => 'test@example.com',
            'emailVerifiedAt' => null,
        ],
    ];

    expect(fn () => $service->store($dataArray))->toThrow(ValidationException::class);
});

it('validates existing email on store', function () {
    $service = app(UserBulkService::class);
    User::factory()->create([
        'name' => 'Sample name',
        'email' => 'test@example.com',
    ]);

    $dataArray = [
        [
            'name' => 'Sample name',
            'email' => 'test@example.com',
            'emailVerifiedAt' => null,
        ],
    ];

    expect(fn () => $service->store($dataArray))->toThrow(ValidationException::class);
});

it('validates missing model IDs on update', function () {
    $service = app(UserBulkService::class);

    expect(fn () => $service->update([
        ['id' => 9999],
    ]))->toThrow(ValidationException::class);
});

it('validates duplicate email on update', function () {
    $service = app(UserBulkService::class);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $dataArray = [
        [
            'id' => $user1->id,
            'name' => 'Sample name updated',
            'email' => 'test_updated@example.com',
            'emailVerifiedAt' => null,
        ],
        [
            'id' => $user2->id,
            'name' => 'Sample name updated',
            'email' => 'test_updated@example.com',
            'emailVerifiedAt' => null,
        ],
    ];

    expect(fn () => $service->update($dataArray))->toThrow(ValidationException::class);
});

it('validates email conflicts on update', function () {
    $service = app(UserBulkService::class);
    $existing = User::factory()->create();
    $user1 = User::factory()->create();

    $dataArray = [
        ['id' => $user1->id, 'email' => $existing->email],
    ];

    expect(fn () => $service->update($dataArray))->toThrow(ValidationException::class);
});

it('validates missing model IDs on delete', function () {
    $service = app(UserBulkService::class);

    expect(fn () => $service->delete([9999]))->toThrow(ValidationException::class);
});

it('stores multiple records successfully', function () {
    $service = app(UserBulkService::class);
    $dataArray = [
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
    ];

    $models = $service->store($dataArray);

    expect($models)->toHaveCount(2);
    expect($models->first())->toBeInstanceOf(User::class);
    expect($models->last())->toBeInstanceOf(User::class);
});

it('updates multiple records successfully', function () {
    $service = app(UserBulkService::class);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $dataArray = [
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
    ];

    $updated = $service->update($dataArray);

    expect($updated)->toHaveCount(2);
    expect($updated->first()->id)->toBe($user1->id);
    expect($updated->last()->id)->toBe($user2->id);
});

it('deletes multiple records and returns count', function () {
    $service = app(UserBulkService::class);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $count = $service->delete([$user1->id, $user2->id]);

    expect($count)->toBe(2);
    $this->assertDatabaseMissing('users', ['id' => $user1->id]);
    $this->assertDatabaseMissing('users', ['id' => $user2->id]);
});
