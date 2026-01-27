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
            'patientId' => ['sometimes', 'uuid', Rule::exists('patients', 'id')],
            'recordDate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'recordType' => ['sometimes', Rule::enum(RecordTypeEnum::class)],
            'caseName' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'totalCost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'remainingAmount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
