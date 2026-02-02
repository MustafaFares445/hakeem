<?php

declare(strict_types=1);

use App\Enums\AppointmentTypeEnum;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $this->tenant->id]);
    grantPermissions($user, 'bookings');
    Sanctum::actingAs($user);
});

it('sorts bookings', function () {
    Booking::factory()->create(['appointment_type' => AppointmentTypeEnum::Preview->value, 'tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['appointment_type' => AppointmentTypeEnum::Surgery->value, 'tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/bookings?sort=appointmentType');
    $response->assertOk();

    $val1 = $response->json('data.0.appointmentType');

    expect($val1)->toBe(AppointmentTypeEnum::Preview->value);

    $response = $this->getJson('/api/bookings?sort=-appointmentType');
    $response->assertOk();

    $val2 = $response->json('data.0.appointmentType');

    expect($val2)->toBe(AppointmentTypeEnum::Surgery->value);
});

it('filters bookings by patient_id', function () {
    $patient = Patient::factory()->create(['tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['patient_id' => null, 'tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['patient_id' => $patient->id, 'tenant_id' => $this->tenant->id]);

    // Use the explicit string "null" to indicate we want rows where patient_id IS NULL
    $response = $this->getJson('/api/bookings?filter[patientId]=null');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters bookings by tenant_id', function () {
    $otherTenant = Tenant::factory()->create();
    Booking::factory()->create(['tenant_id' => null]);
    Booking::factory()->create(['tenant_id' => $otherTenant->id]);

    $response = $this->getJson('/api/bookings?filter[tenantId]=null');

    $response->assertOk();
    // In our multi-tenant setup, bookings always belong to a tenant,
    // so filtering by tenantId = null should yield no results.
    expect($response->json('data'))->toHaveCount(0);
});

it('filters bookings by user_id', function () {
    $otherUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['user_id' => null, 'tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['user_id' => $otherUser->id, 'tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/bookings?filter[userId]=null');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters bookings by date', function () {
    Booking::factory()->create(['date' => '2025-01-01', 'tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/bookings?filter[date]='.('2025-01-01'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters bookings by time', function () {
    Booking::factory()->create(['time' => 'Sample time', 'tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/bookings?filter[time]='.('Sample time'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters bookings by appointment_type', function () {
    Booking::factory()->create(['appointment_type' => AppointmentTypeEnum::Review->value, 'tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['appointment_type' => AppointmentTypeEnum::Preview->value, 'tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/bookings?filter[appointmentType]='.AppointmentTypeEnum::Review->value);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters bookings by date range', function () {
    Booking::factory()->create(['created_at' => now()->subDays(5), 'tenant_id' => $this->tenant->id]);
    Booking::factory()->create(['created_at' => now(), 'tenant_id' => $this->tenant->id]);

    $after = now()->subDays(2)->format('Y-m-d');
    $before = now()->format('Y-m-d');

    $response = $this->getJson('/api/bookings?filter[createdAfter]='.$after.'&filter[createdBefore]='.$before);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('paginates filtered bookings', function () {
    Booking::factory()->count(15)->create(['tenant_id' => $this->tenant->id]);

    $response = $this->getJson('/api/bookings?perPage=5&page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});
