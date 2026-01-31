<?php

declare(strict_types=1);

namespace App\Http\Requests\MediaRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MediaStoreRequest extends FormRequest
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
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'medicalRecordId' => [
                'nullable',
                'uuid',
                Rule::exists('medical_records', 'id')->where('patient_id', $this->input('patientId')),
            ],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:10240'],
            /** @example "attachments" */
            'collection' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
