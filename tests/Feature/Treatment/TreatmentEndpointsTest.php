<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\Treatment;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'treatments');
    Sanctum::actingAs($user);
});

it('lists treatments', function () {
    Treatment::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/treatments');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('name')
            ->and($firstItem)->toHaveKey('description')
            ->and($firstItem)->toHaveKey('defaultCost');
    }
});

it('creates a treatment', function () {
    $payload = [
        'name' => 'Root Canal Treatment',
        'description' => 'Sample treatment description',
        'defaultCost' => 250.50,
    ];

    $response = $this->postJson('/api/treatments', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());

    $id = $response->json('data.id');
    $this->assertDatabaseHas('treatments', ['id' => $id]);
});

it('shows a treatment', function () {
    $treatment = Treatment::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson("/api/treatments/{$treatment->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('description')
        ->and($data)->toHaveKey('defaultCost');
});

it('updates a treatment', function () {
    $treatment = Treatment::factory()->create(['tenant_id' => $this->tenant->id]);

    $updatePayload = [
        'name' => 'Updated Treatment Name',
        'description' => 'Updated description',
        'defaultCost' => 500.00,
    ];

    $response = $this->putJson("/api/treatments/{$treatment->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a treatment', function () {
    $treatment = Treatment::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->deleteJson("/api/treatments/{$treatment->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('treatments', ['id' => $treatment->id]);
});

it('returns 404 when showing non-existent treatment', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->getJson('/api/treatments/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when updating non-existent treatment', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';
    $payload = [
        'name' => 'Updated Treatment Name',
        'description' => 'Updated description',
        'defaultCost' => 500.00,
    ];

    $response = $this->putJson('/api/treatments/'.$nonExistentId, $payload);

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent treatment', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->deleteJson('/api/treatments/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when treatment ID format is invalid', function () {
    $invalidId = 'invalid-uuid';

    $response = $this->getJson('/api/treatments/'.$invalidId);

    $response->assertNotFound();
});
