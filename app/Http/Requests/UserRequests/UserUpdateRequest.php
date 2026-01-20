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
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'emailVerifiedAt' => 'sometimes|nullable|date',
            'primaryImage' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'images' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048', 'array'],
        ];
    }
}
