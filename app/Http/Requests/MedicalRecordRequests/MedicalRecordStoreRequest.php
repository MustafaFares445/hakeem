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
            'patientId' => ['required', 'uuid', Rule::exists('patients', 'id')],
            'recordDate' => ['required', 'date', 'date_format:Y-m-d'],
            'recordType' => ['required', Rule::enum(RecordTypeEnum::class)],
            'caseName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'totalCost' => ['nullable', 'numeric', 'min:0'],
            'remainingAmount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
