<?php

declare(strict_types=1);

namespace App\Http\Requests\MedicalRecordTreatmentRequests;

use App\Enums\ToothPositionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MedicalRecordTreatmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'toothPosition' => ['sometimes', 'nullable', Rule::enum(ToothPositionEnum::class)],
            'treatmentDate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'treatmentCost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'treatmentDescription' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'fillerMaterialId' => ['sometimes', 'nullable', 'uuid', Rule::exists('filler_materials', 'id')],
            'treatmentId' => ['sometimes', 'nullable', 'uuid', Rule::exists('treatments', 'id')],
            'dentalLabId' => ['sometimes', 'nullable', 'uuid', Rule::exists('dental_labs', 'id')],
            'sessionNumber' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'doctorIds' => ['sometimes', 'nullable', 'array'],
            'doctorIds.*' => ['uuid', Rule::exists('users', 'id')],
        ];
    }
}
