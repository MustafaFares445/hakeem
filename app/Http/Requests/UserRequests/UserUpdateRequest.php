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
        $user = $this->route('user') ?? auth()->id();

        return [
            /** @example "Dr. Ahmed Ali" */
            'name' => ['sometimes', 'string', 'max:255'],
            /** @example "ahmed.ali" */
            'username' => ['sometimes', 'string', 'min:3', 'max:191', Rule::unique('users', 'username')->ignore($user)],
            /** @example "ahmed@example.com" */
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            /** @example "+963912345678" */
            'phoneNumber' => ['sometimes', 'nullable', 'string', 'max:20', Rule::unique('users', 'phone_number')->ignore($user)],
            /** @example "en" */
            'language' => ['sometimes', 'string', Rule::in(['en', 'ar'])],
            /** @example "12hr" */
            'timeFormat' => ['sometimes', 'string', Rule::in(['12hr', '24hr'])],
            /** @example ["doctor"] */
            'roles' => ['sometimes', 'nullable', 'array'],
            /** @example "doctor" */
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'primaryImage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
