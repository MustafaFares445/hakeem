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
            'patientId' => ['required', 'uuid', Rule::exists('patients', 'id')],
            'medicalRecordId' => [
                'nullable',
                'uuid',
                Rule::exists('medical_records', 'id')->where('patient_id', $this->input('patientId')),
            ],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:10240'],
            'collection' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
