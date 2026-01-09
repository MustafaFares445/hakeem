<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserBulkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:users,id',
            'items.*.name' => 'sometimes|string|max:255',
            'items.*.email' => ['sometimes|email:rfc,dns|max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'items.*.emailVerifiedAt' => 'sometimes|nullable|date|date_format:Y-m-d H:i:s',
            'items.*.primaryImage' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'items.*.images' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048', 'array'],
        ];
    }
}

