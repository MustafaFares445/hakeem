<?php

declare(strict_types=1);

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;

final class UserBulkDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:users,id',
        ];
    }
}
