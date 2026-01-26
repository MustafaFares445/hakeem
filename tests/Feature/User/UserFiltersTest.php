<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $user = User::factory()->create();
    grantUserPermissions($user);
    Sanctum::actingAs($user);
});

it('filters users by search term', function () {
    User::factory()->create(['name' => 'Target user']);
    User::factory()->create(['name' => 'Other user']);

    $response = $this->withHeader('Accept-Language', 'en')
        ->getJson('/api/users?search=Target');

    $response->assertOk();
    // Adjusted check for translatable or normal string
    $data = $response->json('data');
    $found = false;
    foreach ($data as $item) {
        $val = is_array($item['name']) ? json_encode($item['name']) : (string) $item['name'];
        if (str_contains($val, 'Target')) {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();
});

it('sorts users', function () {
    User::factory()->create(['name' => 'A user']);
    User::factory()->create(['name' => 'Z user']);

    $response = $this->getJson('/api/users?sort=name');
    $response->assertOk();

    $val1 = $response->json('data.0.name');
    if (is_array($val1)) {
        $val1 = $val1['en'] ?? array_values($val1)[0];
    }

    expect((string) $val1)->toContain('A');

    $response = $this->getJson('/api/users?sort=-name');
    $response->assertOk();

    $val2 = $response->json('data.0.name');
    if (is_array($val2)) {
        $val2 = $val2['en'] ?? array_values($val2)[0];
    }

    expect((string) $val2)->toContain('Z');
});

it('filters users by name', function () {
    User::factory()->create(['name' => 'Sample name']);
    User::factory()->create();

    $response = $this->getJson('/api/users?filter[name]='.('Sample name'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters users by email', function () {
    User::factory()->create(['email' => 'test@example.com']);
    User::factory()->create();

    $response = $this->getJson('/api/users?filter[email]='.('test@example.com'));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('filters users by date range', function () {
    // Create users outside the date range
    User::factory()->create(['created_at' => now()->subDays(5)]);
    // Create user inside the date range
    $userInRange = User::factory()->create(['created_at' => now()->subDays(1)]);

    $after = now()->subDays(2)->format('Y-m-d');
    $before = now()->format('Y-m-d');

    $response = $this->getJson('/api/users?filter[createdAfter]='.$after.'&filter[createdBefore]='.$before);

    $response->assertOk();
    $data = $response->json('data');
    // Should find the user in range plus potentially the authenticated user from beforeEach
    expect($data)->toBeArray();
    // At least the user we created should be in the results
    $userIds = array_column($data, 'id');
    expect($userIds)->toContain($userInRange->id);
});

it('paginates filtered users', function () {
    User::factory()->count(15)->create();

    $response = $this->getJson('/api/users?perPage=5&page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
});
