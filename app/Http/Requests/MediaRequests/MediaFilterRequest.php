<?php

declare(strict_types=1);

namespace App\Http\Requests\MediaRequests;

use Illuminate\Foundation\Http\FormRequest;

final class MediaFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'filter.patientAndMedicalRecords' => ['required', 'uuid', 'exists:patients,id'],
            'filter.collectionName' => 'sometimes|string|max:255',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date',
            'sort' => 'sometimes|string',
        ];
    }
}
