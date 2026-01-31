<?php

declare(strict_types=1);

namespace App\Http\Requests\MedicalRecordRequests;

use App\Enums\RecordTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MedicalRecordStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => ['required', 'uuid', Rule::exists('patients', 'id')],
            /** @example "2025-01-31" */
            'recordDate' => ['required', 'date', 'date_format:Y-m-d'],
            /** @example "in_clinic" */
            'recordType' => ['required', Rule::enum(RecordTypeEnum::class)],
            /** @example "Initial examination" */
            'caseName' => ['required', 'string', 'max:255'],
            /** @example "Patient presented with tooth pain" */
            'description' => ['nullable', 'string', 'max:1000'],
            /** @example 200.00 */
            'totalCost' => ['nullable', 'numeric', 'min:0'],
            /** @example 150.00 */
            'paidAmount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
