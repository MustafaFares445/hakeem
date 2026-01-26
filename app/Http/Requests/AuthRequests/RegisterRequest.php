<?php

declare(strict_types=1);

namespace App\Http\Requests\AuthRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'phoneNumber' => ['required', 'string', 'max:191', Rule::unique('users', 'phone_number')],
            'password' => ['required', 'string', 'min:8', 'max:191', 'confirmed'],
        ];
    }
}
