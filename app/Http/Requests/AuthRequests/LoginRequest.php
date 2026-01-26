<?php

declare(strict_types=1);

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:191', Rule::exists('users', 'username')],
            'password' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }
}
