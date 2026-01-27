<?php

declare(strict_types=1);

namespace App\Http\Requests\TreatmentRequests;

use Illuminate\Foundation\Http\FormRequest;

final class TreatmentFilterRequest extends FormRequest
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
            'filter.description' => 'sometimes|string|max:1000',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date',
            'sort' => 'sometimes|string',
        ];
    }
}
