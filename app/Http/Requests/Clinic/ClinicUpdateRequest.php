<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinic;

use Illuminate\Foundation\Http\FormRequest;

final class ClinicUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'clinicName' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phoneNumber' => ['sometimes', 'nullable', 'string', 'max:50'],
            'phoneNumber2' => ['sometimes', 'nullable', 'string', 'max:50'],
            'specialties' => ['sometimes', 'nullable', 'array'],
            'specialties.*' => ['string', 'max:255'],
            'mapPin' => ['sometimes', 'nullable', 'array'],
            'mapPin.lat' => ['sometimes', 'nullable'],
            'mapPin.lng' => ['sometimes', 'nullable'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string'],
            'instagram' => ['sometimes', 'nullable', 'string', 'max:255'],
            'facebook' => ['sometimes', 'nullable', 'string', 'max:255'],
            'startWorkingDay' => ['sometimes', 'nullable', 'string', 'max:100'],
            'endWorkingDay' => ['sometimes', 'nullable', 'string', 'max:100'],
            'startWorkingTime' => ['sometimes', 'nullable', 'date_format:H:i'],
            'endWorkingTime' => ['sometimes', 'nullable', 'date_format:H:i'],
            'primaryImage' => ['nullable', 'image', 'mimes:png,jpg,svg,webp', 'max:3000'],
        ];
    }
}
