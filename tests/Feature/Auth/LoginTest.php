<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('can login with username', function (): void {
    $password = 'password123';
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'username' => 'testuser',
        'password' => $password,
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'user',
                'token',
            ],
            'message',
        ])
        ->assertJson([
            'message' => __('Log in successfully'),
        ]);

    expect($response->json('data.user.id'))->toBe($user->id);
    expect($response->json('data.token'))->not->toBeEmpty();
});

it('requires username', function (): void {
    $response = $this->postJson('/api/auth/login', [
        'password' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);
});

it('fails when username does not exist', function (): void {
    $response = $this->postJson('/api/auth/login', [
        'username' => 'nonexistent',
        'password' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);
});

it('fails with incorrect password', function (): void {
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make('correctpassword'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'username' => 'testuser',
        'password' => 'wrongpassword',
    ]);

    $response->assertUnauthorized();
});

it('requires password', function (): void {
    $response = $this->postJson('/api/auth/login', [
        'username' => 'testuser',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('validates username minimum length', function (): void {
    $response = $this->postJson('/api/auth/login', [
        'username' => 'ab',
        'password' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);
});

it('validates username maximum length', function (): void {
    $response = $this->postJson('/api/auth/login', [
        'username' => str_repeat('a', 192),
        'password' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);
});
