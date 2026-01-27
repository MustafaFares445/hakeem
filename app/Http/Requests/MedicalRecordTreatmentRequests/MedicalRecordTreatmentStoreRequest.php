<?php

declare(strict_types=1);

namespace App\Http\Requests\MedicalRecordTreatmentRequests;

use App\Enums\ToothPositionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MedicalRecordTreatmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medicalRecordId' => ['required', 'uuid', Rule::exists('medical_records', 'id')],
            'toothPosition' => ['nullable', Rule::enum(ToothPositionEnum::class)],
            'treatmentDate' => ['required', 'date', 'date_format:Y-m-d'],
            'treatmentCost' => ['nullable', 'numeric', 'min:0'],
            'treatmentDescription' => ['nullable', 'string', 'max:1000'],
            'fillerMaterialId' => ['required', 'uuid', Rule::exists('filler_materials', 'id')],
            'treatmentId' => ['nullable', 'uuid', Rule::exists('treatments', 'id')],
            'dentalLabId' => ['nullable', 'uuid', Rule::exists('dental_labs', 'id')],
            'sessionNumber' => ['nullable', 'integer', 'min:1'],
            'doctorIds' => ['nullable', 'array'],
            'doctorIds.*' => ['uuid', Rule::exists('users', 'id')],
        ];
    }
}
