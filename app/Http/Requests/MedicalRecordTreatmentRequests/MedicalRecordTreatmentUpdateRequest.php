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
            /** @example "upper_left_molar" */
            'toothPosition' => ['sometimes', 'nullable', Rule::enum(ToothPositionEnum::class)],
            /** @example "2025-01-31" */
            'treatmentDate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            /** @example 100.00 */
            'treatmentCost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            /** @example "Updated treatment notes" */
            'treatmentDescription' => ['sometimes', 'nullable', 'string', 'max:1000'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'fillerMaterialId' => ['sometimes', 'nullable', 'uuid', Rule::exists('filler_materials', 'id')],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'treatmentId' => ['sometimes', 'nullable', 'uuid', Rule::exists('treatments', 'id')],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'dentalLabId' => ['sometimes', 'nullable', 'uuid', Rule::exists('dental_labs', 'id')],
            /** @example 1 */
            'sessionNumber' => ['sometimes', 'nullable', 'integer', 'min:1'],
            /** @example ["9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f"] */
            'doctorIds' => ['sometimes', 'nullable', 'array'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'doctorIds.*' => ['uuid', Rule::exists('users', 'id')],
        ];
    }
}
