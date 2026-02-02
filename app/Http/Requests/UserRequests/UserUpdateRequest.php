<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Dr. Ahmed Ali" */
            'name' => ['sometimes', 'string', 'max:255'],
            /** @example "ahmed.ali" */
            'username' => ['sometimes', 'string', 'min:3', 'max:191', Rule::unique('users', 'username')->ignore($this->route('user'))],
            /** @example "ahmed@example.com" */
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            /** @example ["doctor"] */
            'roles' => ['sometimes', 'nullable', 'array'],
            /** @example "doctor" */
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'primaryImage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
