<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;

final class UserBulkUpdateRequest extends FormRequest
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
            'items.*.email' => ['sometimes', 'email', 'max:255', 'distinct'],
            'items.*.emailVerifiedAt' => 'sometimes|nullable|date',
            'items.*.primaryImage' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'items.*.images' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048', 'array'],
        ];
    }
}
