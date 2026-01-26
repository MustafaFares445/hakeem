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
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('patients', 'email')->ignore($this->route('patient'))],
            'phoneNumber' => ['sometimes', 'string', 'max:255', Rule::unique('patients', 'phone_number')->ignore($this->route('patient'))],
            'birthday' => ['sometimes', 'nullable', 'date', 'date_format:Y-m-d'],
            'gender' => [Rule::enum(PatientGenderEnum::class)],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'streetAddress' => ['sometimes', 'nullable', 'string', 'max:255'],
            'registrationDate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primaryImage' => ['sometimes', 'nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }
}
