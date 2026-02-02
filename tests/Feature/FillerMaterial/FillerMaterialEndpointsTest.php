<?php

declare(strict_types=1);

use App\Models\FillerMaterial;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);

    $permissions = [
        'filler-materials.viewAny',
        'filler-materials.view',
        'filler-materials.create',
        'filler-materials.update',
        'filler-materials.delete',
    ];

    $guardName = config('auth.defaults.guard', 'web');

    foreach ($permissions as $name) {
        $permission = Permission::firstOrCreate(['name' => $name, 'guard_name' => $guardName]);
        $user->givePermissionTo($permission);
    }

    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    $user->load('permissions');

    Sanctum::actingAs($user);
});

it('lists filler materials', function () {
    FillerMaterial::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/filler-materials');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('name')
            ->and($firstItem)->toHaveKey('description')
            ->and($firstItem)->toHaveKey('isActive');
    }
});

it('creates a filler material', function () {
    $payload = [
        'name' => 'Sample Filler Material',
        'description' => 'Sample description',
        'isActive' => true,
    ];

    $response = $this->postJson('/api/filler-materials', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());

    $id = $response->json('data.id');
    $this->assertDatabaseHas('filler_materials', ['id' => $id]);
});

it('shows a filler material', function () {
    $fillerMaterial = FillerMaterial::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson("/api/filler-materials/{$fillerMaterial->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('description')
        ->and($data)->toHaveKey('isActive');
});

it('updates a filler material', function () {
    $fillerMaterial = FillerMaterial::factory()->create(['tenant_id' => $this->tenant->id]);

    $updatePayload = [
        'name' => 'Updated Filler Material',
        'description' => 'Updated description',
        'isActive' => false,
    ];

    $response = $this->putJson("/api/filler-materials/{$fillerMaterial->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a filler material', function () {
    $fillerMaterial = FillerMaterial::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->deleteJson("/api/filler-materials/{$fillerMaterial->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('filler_materials', ['id' => $fillerMaterial->id]);
});

it('returns 404 when showing non-existent filler material', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->getJson('/api/filler-materials/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when updating non-existent filler material', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';
    $payload = [
        'name' => 'Updated Filler Material',
        'description' => 'Updated description',
        'isActive' => false,
    ];

    $response = $this->putJson('/api/filler-materials/'.$nonExistentId, $payload);

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent filler material', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->deleteJson('/api/filler-materials/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when filler material ID format is invalid', function () {
    $invalidId = 'invalid-uuid';

    $response = $this->getJson('/api/filler-materials/'.$invalidId);

    $response->assertNotFound();
});
