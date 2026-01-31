<?php

declare(strict_types=1);

namespace App\Http\Requests\PatientRequests;

use App\Enums\PatientGenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PatientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Omar Hassan" */
            'name' => ['sometimes', 'string', 'max:255'],
            /** @example "omar@example.com" */
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('patients', 'email')->ignore($this->route('patient'))],
            /** @example "+966501234567" */
            'phoneNumber' => ['sometimes', 'string', 'max:255', Rule::unique('patients', 'phone_number')->ignore($this->route('patient'))],
            /** @example "1990-05-15" */
            'birthday' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            /** @example "male" */
            'gender' => [Rule::enum(PatientGenderEnum::class)],
            /** @example "Riyadh" */
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            /** @example "King Fahd Road" */
            'streetAddress' => ['sometimes', 'nullable', 'string', 'max:255'],
            /** @example "2025-01-15" */
            'registrationDate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            /** @example "Updated notes" */
            'notes' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primaryImage' => ['sometimes', 'nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }
}
