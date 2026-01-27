<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

beforeEach(/**
 * @throws JsonException
 * @throws TenantCouldNotBeIdentifiedById
 */ function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $tenant = Tenant::factory()->create();
    tenancy()->initialize($tenant);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    Sanctum::actingAs($user);
});

it('forbids unauthorized user from viewing users', function () {
    // Arrange
    $user = User::factory()->create();
    User::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->getJson('/api/users');

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from creating user', function () {
    // Arrange
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'username' => 'testuser',
        'roles' => [RoleEnum::cases()[0]->value,]
    ];

    // Act
    $response = $this->postJson('/api/users', $payload);

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from updating user', function () {
    // Arrange
    $user = User::factory()->create();
    $model = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Sample name',
        'email' => 'test@example.com',
        'username' => 'testuser',
        'roles' => [RoleEnum::cases()[0]->value,]
    ];

    // Act
    $response = $this->putJson('/api/users/'.$model->id, $payload);

    // Assert
    $response->assertForbidden();
});

it('forbids unauthorized user from deleting user', function () {
    // Arrange
    $user = User::factory()->create();
    $model = User::factory()->create();
    Sanctum::actingAs($user);

    // Act
    $response = $this->deleteJson('/api/users/'.$model->id);

    // Assert
    $response->assertForbidden();
});
