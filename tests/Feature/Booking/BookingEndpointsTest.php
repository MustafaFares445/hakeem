<?php

declare(strict_types=1);

use App\Enums\AppointmentTypeEnum;
use App\Models\Booking;
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
    grantPermissions($user, 'bookings');
    Sanctum::actingAs($user);
});

it('lists bookings', function () {
    Booking::factory()->count(3)->create();

    $response = $this->getJson('/api/bookings');

    $response->assertOk()->assertJsonPath('message', ResponseMessages::RETRIEVED->message());
    expect($response->json('data'))->toBeArray();

    $data = $response->json('data');
    if (! empty($data)) {
        $firstItem = $data[0];
        expect($firstItem['id'])->toBeString()
            ->and($firstItem)->toHaveKey('patientId')
            ->and($firstItem)->toHaveKey('tenantId')
            ->and($firstItem)->toHaveKey('userId')
            ->and($firstItem)->toHaveKey('date')
            ->and($firstItem)->toHaveKey('time')
            ->and($firstItem)->toHaveKey('appointmentType');

    }
});

it('creates a booking', function () {
    $payload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time',
        'appointmentType' => AppointmentTypeEnum::Preview->value,
    ];

    $response = $this->postJson('/api/bookings', $payload);
    $response->assertCreated()->assertJsonPath('message', ResponseMessages::CREATED->message());
    $id = $response->json('data.id');
    $this->assertDatabaseHas('bookings', ['id' => $id]);
});

it('shows a booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->getJson("/api/bookings/{$booking->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::RETRIEVED->message());

    $data = $response->json('data');

    expect($data['id'])->toBeString()
        ->and($data)->toHaveKey('patientId')
        ->and($data)->toHaveKey('tenantId')
        ->and($data)->toHaveKey('userId')
        ->and($data)->toHaveKey('date')
        ->and($data)->toHaveKey('time')
        ->and($data)->toHaveKey('appointmentType');

});

it('updates a booking', function () {
    $booking = Booking::factory()->create();

    $updatePayload = [
        'patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time updated',
        'appointmentType' => AppointmentTypeEnum::Surgery->value,
    ];

    $response = $this->putJson("/api/bookings/{$booking->id}", $updatePayload);
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::UPDATED->message());
});

it('deletes a booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->deleteJson("/api/bookings/{$booking->id}");
    $response->assertOk()
        ->assertJsonPath('message', ResponseMessages::DELETED->message());

    $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
});
it('returns 404 when showing non-existent booking', function () {
    // Arrange
    $nonExistentId = 99999;

    // Act
    $response = $this->getJson('/api/bookings/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when updating non-existent booking', function () {
    // Arrange
    $nonExistentId = 99999;
    $payload = ['patientId' => null,
        'tenantId' => null,
        'userId' => null,
        'date' => '2025-01-01',
        'time' => 'Sample time updated',
        'appointmentType' => AppointmentTypeEnum::Review->value, ];

    // Act
    $response = $this->putJson('/api/bookings/'.$nonExistentId, $payload);

    // Assert
    $response->assertNotFound();
});

it('returns 404 when deleting non-existent booking', function () {
    // Arrange
    $nonExistentId = 99999;

    // Act
    $response = $this->deleteJson('/api/bookings/'.$nonExistentId);

    // Assert
    $response->assertNotFound();
});
