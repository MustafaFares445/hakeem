<?php

declare(strict_types=1);

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;

final class ForgotPasswordRequest extends FormRequest
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
        ];
    }
}
