<?php

declare(strict_types=1);

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;

final class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "user@example.com" */
            'email' => ['required', 'email', 'exists:users,email'],
            /** @example "123456" */
            'otp' => ['required', 'string', 'size:6'],
            /** @example "newPassword123" */
            'password' => ['required', 'string', 'min:8', 'max:191', 'confirmed'],
        ];
    }
}
