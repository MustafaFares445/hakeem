<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Auth\LoginData;
use App\Data\Auth\RegisterData;
use App\Data\Auth\ResetPasswordData;
use App\Data\SubscriptionAccessResult;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\PersonalAccessToken;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

final class AuthService
{
    public function __construct(private readonly SubscriptionAccessService $subscriptionAccessService) {}

    /**
     * @return array{
     *     user: User,
     *     token: string,
     *     subscriptionStatus: SubscriptionAccessResult
     * }
     */
    public function login(LoginData $data): array
    {
        $user = User::query()
            ->where('username', $data->username)
            ->firstOrFail();

        Gate::forUser($user)->authorize('attempt-login', [$data->password]);

        return [
            'user' => $user->load('tenant.tenantType'),
            'token' => $user->createToken('api-token')->plainTextToken,
            'subscriptionStatus' => $this->subscriptionAccessService->forUser($user),
        ];
    }

    public function logout(?PersonalAccessToken $token): void
    {
        $token?->delete();
    }

    /**
     * @return array{httpStatus: int, status: string}
     *
     * @throws RandomException
     */
    public function sendResetLink(string $email): array
    {
        $user = User::query()->where('email', $email)->firstOrFail();

        $otp = $this->generateOtp();

        Cache::put(
            'password_reset_otp:'.$email,
            $otp,
            now()->addMinutes(15)
        );

        $user->notify(new ResetPasswordNotification($otp));

        return [
            'httpStatus' => ResponseAlias::HTTP_OK,
            'status' => __('Password reset code sent successfully'),
        ];
    }

    /**
     * @return array{httpStatus: int, status: string}
     *
     * @throws AuthorizationException
     */
    public function resetPassword(ResetPasswordData $data): array
    {
        $user = User::query()->where('email', $data->email)->firstOrFail();

        $cacheKey = 'password_reset_otp:'.$data->email;
        $storedOtp = Cache::get($cacheKey);

        if ($storedOtp === null || ! hash_equals($storedOtp, $data->otp)) {
            throw new AuthorizationException(__('Invalid reset code or reset code has expired'));
        }

        Cache::forget($cacheKey);

        $user->update(['password' => $data->password]);

        event(new PasswordReset($user));

        return [
            'httpStatus' => ResponseAlias::HTTP_OK,
            'status' => __('Password reset successfully'),
        ];
    }

    public function register(RegisterData $data): User
    {
        return User::query()->create([
            'name' => $data->name,
            'phone_number' => $data->phoneNumber,
            'password' => $data->password,
        ]);
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $user->update(['password' => $newPassword]);

        event(new PasswordReset($user));
    }

    /**
     * @throws RandomException
     */
    private function generateOtp(): string
    {
        return mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
