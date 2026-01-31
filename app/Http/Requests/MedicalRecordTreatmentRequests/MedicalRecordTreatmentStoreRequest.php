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
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'medicalRecordId' => ['required', 'uuid', Rule::exists('medical_records', 'id')],
            /** @example "upper_left_molar" */
            'toothPosition' => ['nullable', Rule::enum(ToothPositionEnum::class)],
            /** @example "2025-01-31" */
            'treatmentDate' => ['required', 'date', 'date_format:Y-m-d'],
            /** @example 100.00 */
            'treatmentCost' => ['nullable', 'numeric', 'min:0'],
            /** @example "Completed successfully" */
            'treatmentDescription' => ['nullable', 'string', 'max:1000'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'fillerMaterialId' => ['required', 'uuid', Rule::exists('filler_materials', 'id')],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'treatmentId' => ['nullable', 'uuid', Rule::exists('treatments', 'id')],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'dentalLabId' => ['nullable', 'uuid', Rule::exists('dental_labs', 'id')],
            /** @example 1 */
            'sessionNumber' => ['nullable', 'integer', 'min:1'],
            /** @example ["9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f"] */
            'doctorIds' => ['nullable', 'array'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'doctorIds.*' => ['uuid', Rule::exists('users', 'id')],
        ];
    }
}
