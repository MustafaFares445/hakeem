<?php

declare(strict_types=1);

namespace App\Http\Requests\SubscriptionRequests;

use Illuminate\Foundation\Http\FormRequest;

final class SubscriptionOrderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'perPage' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'filter.status' => ['sometimes', 'nullable', 'string', 'in:pending,confirmed,cancelled'],
            'sort' => ['sometimes', 'string', 'in:createdAt,-createdAt,status,-status'],
        ];
    }
}
