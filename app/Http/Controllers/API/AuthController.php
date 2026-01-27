<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\Auth\LoginData;
use App\Data\Auth\ResetPasswordData;
use App\Http\Requests\AuthRequests\ForgotPasswordRequest;
use App\Http\Requests\AuthRequests\LoginRequest;
use App\Http\Requests\AuthRequests\ResetPasswordRequest;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mrmarchone\LaravelAutoCrud\Traits\MessageTrait;

final class AuthController
{
    use MessageTrait;

    public function __construct(protected AuthService $authService) {}

    /**
     * Authenticate user and generate access token
     *
     * This endpoint authenticates a user with their username and password. Upon successful authentication,
     * it returns a Bearer token that can be used for subsequent authenticated requests.
     *
     * @operation login
     *
     * @tags API
     *
     * @unauthenticated
     *
     * @throws AuthenticationException 401 Invalid credentials provided
     * @throws AuthorizationException 403 Email address not verified (for non-admin users)
     */
    public function login(LoginRequest $request): AuthResource
    {
        $authResult = $this->authService->login(LoginData::from($request->validated()));

        return AuthResource::make($authResult)
            ->additional(['message' => __('Log in successfully')]);
    }

    /**
     * Logout authenticated user
     *
     * This endpoint invalidates the current access token, effectively logging out the user.
     * The user must be authenticated to access this endpoint.
     *
     * @operation logout
     *
     * @tags API
     *
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user()?->currentAccessToken());

        return $this->successMessage(message: __('Logged out successfully'));
    }

    /**
     * Request password reset code
     *
     * This endpoint sends a password reset OTP code to the user's email address.
     * The reset code can be used to reset the user's password.
     * The code expire after 15 minutes
     *
     * @operation forgotPassword
     *
     * @tags API
     *
     * @unauthenticated
     *
     * @throws ValidationException 422 Invalid email address does not exist
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $responseData = $this->authService->sendResetLink($request->validated('email'));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }

    /**
     * Reset user password
     *
     * This endpoint allows a user to reset their password using a valid reset OTP code
     * that was sent to their email address via the forgot password endpoint.
     *
     * @operation resetPassword
     *
     * @tags API
     *
     * @unauthenticated
     *
     * @throws AuthorizationException 403 Invalid reset code or reset code has expired
     * @throws ValidationException 422 Invalid email, OTP format, or password validation failed
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $responseData = $this->authService->resetPassword(ResetPasswordData::from($request->validated()));

        return $this->successMessage(message: __($responseData['status']), status: $responseData['httpStatus']);
    }
}
