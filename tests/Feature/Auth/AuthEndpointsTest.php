<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

describe('Login', function (): void {
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
});

describe('Logout', function (): void {
    it('requires authentication', function (): void {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized();
    });
});

describe('Change Password', function (): void {
    it('can change password when authenticated', function (): void {
        Event::fake();

        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/auth/change-password', [
            'currentPassword' => 'oldpassword',
            'newPassword' => 'newpassword123',
            'newPassword_confirmation' => 'newpassword123',
        ]);

        $response->assertSuccessful()
            ->assertJson([
                'message' => __('Password changed successfully'),
            ]);

        expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
        Event::assertDispatched(PasswordReset::class);
    });

    it('requires authentication', function (): void {
        $response = $this->putJson('/api/auth/change-password', [
            'currentPassword' => 'oldpassword',
            'newPassword' => 'newpassword123',
            'newPassword_confirmation' => 'newpassword123',
        ]);

        $response->assertUnauthorized();
    });

    it('fails with incorrect current password', function (): void {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($user)->putJson('/api/auth/change-password', [
            'currentPassword' => 'wrongpassword',
            'newPassword' => 'newpassword123',
            'newPassword_confirmation' => 'newpassword123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['currentPassword']);
    });
});

describe('Forgot Password', function (): void {
    beforeEach(function (): void {
        Notification::fake();
    });

    it('sends password reset OTP to user email', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/auth/forget-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertSuccessful()
            ->assertJson([
                'message' => __('Password reset code sent successfully'),
            ]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);

        $cacheKey = 'password_reset_otp:test@example.com';
        expect(Cache::has($cacheKey))->toBeTrue();
        expect(Cache::get($cacheKey))->toMatch('/^\d{6}$/');
    });

    it('requires email', function (): void {
        $response = $this->postJson('/api/auth/forget-password', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('validates email format', function (): void {
        $response = $this->postJson('/api/auth/forget-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('fails when email does not exist', function (): void {
        $response = $this->postJson('/api/auth/forget-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('stores OTP in cache for 15 minutes', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->postJson('/api/auth/forget-password', [
            'email' => 'test@example.com',
        ]);

        $cacheKey = 'password_reset_otp:test@example.com';
        expect(Cache::has($cacheKey))->toBeTrue();

        $this->travel(16)->minutes();

        expect(Cache::has($cacheKey))->toBeFalse();
    });
});

describe('Reset Password', function (): void {
    beforeEach(function (): void {
        Event::fake();
    });

    it('can reset password with valid OTP', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $otp = '123456';
        Cache::put('password_reset_otp:test@example.com', $otp, now()->addMinutes(15));

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => $otp,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSuccessful()
            ->assertJson([
                'message' => __('Password reset successfully'),
            ]);

        expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
        expect(Cache::has('password_reset_otp:test@example.com'))->toBeFalse();

        Event::assertDispatched(PasswordReset::class);
    });

    it('requires email', function (): void {
        $response = $this->postJson('/api/auth/reset-password', [
            'otp' => '123456',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('requires OTP', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['otp']);
    });

    it('requires password', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $otp = '123456';
        Cache::put('password_reset_otp:test@example.com', $otp, now()->addMinutes(15));

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => $otp,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });

    it('validates email format', function (): void {
        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'invalid-email',
            'otp' => '123456',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('fails when email does not exist', function (): void {
        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'nonexistent@example.com',
            'otp' => '123456',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    });

    it('validates OTP length', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => '12345',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['otp']);
    });

    it('validates password minimum length', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $otp = '123456';
        Cache::put('password_reset_otp:test@example.com', $otp, now()->addMinutes(15));

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => $otp,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });

    it('validates password confirmation', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $otp = '123456';
        Cache::put('password_reset_otp:test@example.com', $otp, now()->addMinutes(15));

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => $otp,
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });

    it('fails with invalid OTP', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $correctOtp = '123456';
        Cache::put('password_reset_otp:test@example.com', $correctOtp, now()->addMinutes(15));

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => '999999',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertForbidden();
    });

    it('fails with expired OTP', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $otp = '123456';
        Cache::put('password_reset_otp:test@example.com', $otp, now()->addMinutes(15));

        $this->travel(16)->minutes();

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => $otp,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertForbidden();
    });

    it('fails when no OTP exists for email', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => '123456',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertForbidden();
    });

    it('deletes OTP from cache after successful reset', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $otp = '123456';
        $cacheKey = 'password_reset_otp:test@example.com';
        Cache::put($cacheKey, $otp, now()->addMinutes(15));

        $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => $otp,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        expect(Cache::has($cacheKey))->toBeFalse();
    });

    it('validates password maximum length', function (): void {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $otp = '123456';
        Cache::put('password_reset_otp:test@example.com', $otp, now()->addMinutes(15));

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'test@example.com',
            'otp' => $otp,
            'password' => str_repeat('a', 192),
            'password_confirmation' => str_repeat('a', 192),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    });
});
