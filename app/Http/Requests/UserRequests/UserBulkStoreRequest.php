<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UserBulkStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.email' => ['required', 'email', 'max:255', 'distinct', Rule::unique('users', 'email')],
            'items.*.emailVerifiedAt' => 'nullable|date',
            'items.*.primaryImage' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'items.*.images' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048', 'array'],
        ];
    }
}
