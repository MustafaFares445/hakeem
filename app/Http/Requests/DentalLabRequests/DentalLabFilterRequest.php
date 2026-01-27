<?php

declare(strict_types=1);

namespace App\Http\Requests\DentalLabRequests;

use Illuminate\Foundation\Http\FormRequest;

final class DentalLabFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'filter.name' => 'sometimes|string|max:255',
            'filter.phone' => 'sometimes|string|max:20',
            'filter.address' => 'sometimes|string|max:255',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date',
            'sort' => 'sometimes|string',
        ];
    }
}
