<?php

declare(strict_types=1);

use App\Models\DentalLab;
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
    grantPermissions($user, 'dental_labs');
    Sanctum::actingAs($user);
});

it('lists dental labs', function () {
    DentalLab::factory()->count(3)->create();

    $response = $this->getJson('/api/dental-labs');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('name')
            ->and($firstItem)->toHaveKey('phone')
            ->and($firstItem)->toHaveKey('address');
    }
});

it('creates a dental lab', function () {
    $payload = [
        'name' => 'Sample Dental Lab',
        'phone' => '+1234567890',
        'address' => 'Sample address',
    ];

    $response = $this->postJson('/api/dental-labs', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());

    $id = $response->json('data.id');
    $this->assertDatabaseHas('dental_labs', ['id' => $id]);
});

it('shows a dental lab', function () {
    $dentalLab = DentalLab::factory()->create();

    $response = $this->getJson("/api/dental-labs/{$dentalLab->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('name')
        ->and($data)->toHaveKey('phone')
        ->and($data)->toHaveKey('address');
});

it('updates a dental lab', function () {
    $dentalLab = DentalLab::factory()->create();

    $updatePayload = [
        'name' => 'Updated Dental Lab',
        'phone' => '+1987654321',
        'address' => 'Updated address',
    ];

    $response = $this->putJson("/api/dental-labs/{$dentalLab->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a dental lab', function () {
    $dentalLab = DentalLab::factory()->create();

    $response = $this->deleteJson("/api/dental-labs/{$dentalLab->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('dental_labs', ['id' => $dentalLab->id]);
});

it('returns 404 when showing non-existent dental lab', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->getJson('/api/dental-labs/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when updating non-existent dental lab', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';
    $payload = [
        'name' => 'Updated Dental Lab',
        'phone' => '+1987654321',
        'address' => 'Updated address',
    ];

    $response = $this->putJson('/api/dental-labs/'.$nonExistentId, $payload);

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent dental lab', function () {
    $nonExistentId = '00000000-0000-0000-0000-000000000000';

    $response = $this->deleteJson('/api/dental-labs/'.$nonExistentId);

    $response->assertNotFound();
});

it('returns 404 when dental lab ID format is invalid', function () {
    $invalidId = 'invalid-uuid';

    $response = $this->getJson('/api/dental-labs/'.$invalidId);

    $response->assertNotFound();
});

