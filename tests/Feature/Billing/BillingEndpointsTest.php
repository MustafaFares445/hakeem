<?php

declare(strict_types=1);

use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use App\Models\Billing;
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
    grantPermissions($user, 'billings');
    Sanctum::actingAs($user);
});

it('lists billings', function () {
    Billing::factory()->count(3)->create();

    $response = $this->getJson('/api/billings');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('type')
            ->and($firstItem)->toHaveKey('date')
            ->and($firstItem)->toHaveKey('createdAt');
    }
});

it('creates an incoming billing', function () {
    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => '2025-01-01',
        'caseName' => 'Deep dental cavity',
        'paidAmount' => 150,
        'totalCost' => 350,
    ];

    $response = $this->postJson('/api/billings', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('billings', ['id' => $id]);
});

it('creates an outgoing billing', function () {
    $payload = [
        'type' => BillingTypeEnum::Outgoing->value,
        'date' => '2025-01-01',
        'itemName' => 'Ibrophine 600',
        'quantity' => 2,
        'amount' => 20,
        'outgoingType' => BillingOutgoingTypeEnum::Medicine->value,
    ];

    $response = $this->postJson('/api/billings', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $this->assertDatabaseHas('billings', ['item_name' => 'Ibrophine 600']);
});

it('shows a billing', function () {
    $billing = Billing::factory()->create();

    $response = $this->getJson("/api/billings/{$billing->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');
    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('type')
        ->and($data)->toHaveKey('date');
});

it('updates a billing', function () {
    $billing = Billing::factory()->incoming()->create();

    $updatePayload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => '2025-01-15',
        'caseName' => 'Updated case name',
        'paidAmount' => 200,
        'totalCost' => 400,
    ];

    $response = $this->putJson("/api/billings/{$billing->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a billing', function () {
    $billing = Billing::factory()->create();

    $response = $this->deleteJson("/api/billings/{$billing->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('billings', ['id' => $billing->id]);
});

it('returns 404 when showing non-existent billing', function () {
    $nonExistentUuid = '00000000-0000-0000-0000-000000000000';

    $response = $this->getJson('/api/billings/'.$nonExistentUuid);

    $response->assertNotFound();
});

it('returns 404 when updating non-existent billing', function () {
    $nonExistentUuid = '00000000-0000-0000-0000-000000000000';
    $payload = [
        'type' => BillingTypeEnum::Incoming->value,
        'date' => '2025-01-01',
        'caseName' => 'Case',
        'paidAmount' => 100,
        'totalCost' => 200,
    ];

    $response = $this->putJson('/api/billings/'.$nonExistentUuid, $payload);

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent billing', function () {
    $nonExistentUuid = '00000000-0000-0000-0000-000000000000';

    $response = $this->deleteJson('/api/billings/'.$nonExistentUuid);

    $response->assertNotFound();
});
