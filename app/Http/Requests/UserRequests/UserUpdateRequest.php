<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes|email:rfc,dns|max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'emailVerifiedAt' => 'sometimes|nullable|date|date_format:Y-m-d H:i:s',
            'primaryImage' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'images' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048', 'array'],
        ];
    }
}
