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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('patients', 'email')],
            'phoneNumber' => ['required', 'string', 'max:255', Rule::unique('patients', 'phone_number')],
            'birthday' => ['nullable', 'date', 'date_format:Y-m-d'],
            'gender' => ['required', Rule::enum(PatientGenderEnum::class)],
            'city' => ['nullable', 'string', 'max:255'],
            'streetAddress' => ['nullable', 'string', 'max:255'],
            'registrationDate' => ['required', 'date', 'date_format:Y-m-d'],
            'notes' => ['nullable', 'string', 'max:255'],
            'primaryImage' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }
}
