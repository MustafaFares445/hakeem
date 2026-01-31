<?php

declare(strict_types=1);

namespace App\Http\Requests\PatientRequests;

use App\Enums\PatientGenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class PatientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Omar Hassan" */
            'name' => ['required', 'string', 'max:255'],
            /** @example "omar@example.com" */
            'email' => ['nullable', 'email', 'max:255', Rule::unique('patients', 'email')],
            /** @example "+966501234567" */
            'phoneNumber' => ['required', 'string', 'max:255', Rule::unique('patients', 'phone_number')],
            /** @example "1990-05-15" */
            'birthday' => ['nullable', 'date', 'date_format:Y-m-d'],
            /** @example "male" */
            'gender' => ['required', Rule::enum(PatientGenderEnum::class)],
            /** @example "Riyadh" */
            'city' => ['nullable', 'string', 'max:255'],
            /** @example "King Fahd Road" */
            'streetAddress' => ['nullable', 'string', 'max:255'],
            /** @example "2025-01-15" */
            'registrationDate' => ['required', 'date', 'date_format:Y-m-d'],
            /** @example "Regular checkup" */
            'notes' => ['nullable', 'string', 'max:255'],
            'primaryImage' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }
}
