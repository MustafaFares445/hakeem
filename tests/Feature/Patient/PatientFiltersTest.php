<?php

declare(strict_types=1);

use App\Enums\PatientGenderEnum;
use App\Models\Patient;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
});

it('sorts patients', function () {
    Patient::factory()->create(['name' => 'A patient']);
    Patient::factory()->create(['name' => 'Z patient']);

    $response = $this->getJson('/api/patients?sort=name');
    $response->assertOk();

    $val1 = $response->json('data.0.name');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/patients?sort=-name');
    $response->assertOk();

    $val2 = $response->json('data.0.name');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});

it('filters patients by name', function () {
    Patient::factory()->create(['name' => 'Sample name']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[name]='.('Sample name'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by email', function () {
    Patient::factory()->create(['email' => 'test@example.com']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[email]='.('test@example.com'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by phone_number', function () {
    Patient::factory()->create(['phone_number' => '+1234567890']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[phoneNumber]='.('+1234567890'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by birthday', function () {
    Patient::factory()->create(['birthday' => '2025-01-01']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[birthday]='.('2025-01-01'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by gender', function () {
    $genderValue = PatientGenderEnum::cases()[0]->value;
    $otherGenderValue = PatientGenderEnum::cases()[1]->value;
    Patient::factory()->create(['gender' => $genderValue]);
    Patient::factory()->create(['gender' => $otherGenderValue]);

    $response = $this->getJson('/api/patients?filter[gender]='.(PatientGenderEnum::cases()[0]->value));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by city', function () {
    Patient::factory()->create(['city' => 'Sample city']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[city]='.('Sample city'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by street_address', function () {
    Patient::factory()->create(['street_address' => 'Sample street_address']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[streetAddress]='.('Sample street_address'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by registration_date', function () {
    Patient::factory()->create(['registration_date' => '2025-01-01']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[registrationDate]='.('2025-01-01'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by notes', function () {
    Patient::factory()->create(['notes' => 'Sample notes']);
    Patient::factory()->create();

    $response = $this->getJson('/api/patients?filter[notes]='.('Sample notes'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters patients by date range', function () {
    Patient::factory()->create(['created_at' => now()->subDays(5)]);
    Patient::factory()->create(['created_at' => now()]);

    $after = now()->subDays(2)->format('Y-m-d');
    $before = now()->format('Y-m-d');

    $response = $this->getJson('/api/patients?filter[createdAfter]='.$after.'&filter[createdBefore]='.$before);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('paginates filtered patients', function () {
    Patient::factory()->count(15)->create();

    $response = $this->getJson('/api/patients?per_page=5&page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});
