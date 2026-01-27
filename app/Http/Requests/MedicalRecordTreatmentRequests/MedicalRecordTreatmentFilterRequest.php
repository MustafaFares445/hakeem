<?php

declare(strict_types=1);

namespace App\Http\Requests\MedicalRecordTreatmentRequests;

use Illuminate\Foundation\Http\FormRequest;

final class MedicalRecordTreatmentFilterRequest extends FormRequest
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
            'filter.medicalRecordId' => 'sometimes|uuid',
            'filter.treatmentId' => 'sometimes|uuid',
            'filter.toothPosition' => 'sometimes|string|max:255',
            'filter.fillerMaterial' => 'sometimes|string|max:255',
            'filter.dentalLabId' => 'sometimes|uuid',
            'filter.treatmentDateAfter' => 'sometimes|date',
            'filter.treatmentDateBefore' => 'sometimes|date',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date',
            'sort' => 'sometimes|string',
        ];
    }
}
