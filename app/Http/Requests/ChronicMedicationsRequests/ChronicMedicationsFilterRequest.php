<?php

declare(strict_types=1);

namespace App\Http\Requests\ChronicMedicationsRequests;

use Illuminate\Foundation\Http\FormRequest;

final class ChronicMedicationsFilterRequest extends FormRequest
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
            'filter.patientId' => 'sometimes|string|max:255',
            'filter.title' => 'sometimes|string|max:255',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
            'filter.search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:patientId,-patientId,title,-title',
        ];
    }
}
