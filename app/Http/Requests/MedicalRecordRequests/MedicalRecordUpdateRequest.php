<?php

declare(strict_types=1);

namespace App\Http\Requests\MedicalRecordRequests;

use App\Enums\RecordTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MedicalRecordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => ['sometimes', 'uuid', Rule::exists('patients', 'id')],
            /** @example "2025-01-31" */
            'recordDate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            /** @example "in_clinic" */
            'recordType' => ['sometimes', Rule::enum(RecordTypeEnum::class)],
            /** @example "Follow-up" */
            'caseName' => ['sometimes', 'string', 'max:255'],
            /** @example "Updated diagnosis notes" */
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            /** @example 250.00 */
            'totalCost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            /** @example 200.00 */
            'paidAmount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
