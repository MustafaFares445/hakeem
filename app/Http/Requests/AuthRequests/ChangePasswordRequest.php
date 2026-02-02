<?php

declare(strict_types=1);

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "currentPassword123" */
            'currentPassword' => ['required', 'string', 'current_password:sanctum'],
            /** @example "newPassword123" */
            'newPassword' => ['required', 'string', Password::min(8), 'confirmed'],
            /** @example "newPassword123" */
            'newPassword_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'currentPassword.current_password' => __('The current password is incorrect.'),
        ];
    }
}
