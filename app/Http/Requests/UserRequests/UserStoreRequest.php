<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Dr. Ahmed Ali" */
            'name' => ['required', 'string', 'max:255'],
            /** @example "ahmed.ali" */
            'username' => ['required', 'string', 'min:3', 'max:191', Rule::unique('users', 'username')],
            /** @example "ahmed@example.com" */
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            /** @example "+963912345678" */
            'phoneNumber' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone_number')],
            /** @example "password" */
            'password' => ['nullable', 'string', 'min:3'],
            /** @example ["doctor"] */
            'roles' => ['required', 'array'],
            /** @example "doctor" */
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'primaryImage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }
}
