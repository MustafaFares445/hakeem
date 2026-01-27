<?php

declare(strict_types=1);

namespace App\Http\Requests\BookingRequests;

use Illuminate\Foundation\Http\FormRequest;

final class BookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patientId' => ['sometimes', 'nullable', 'string', 'max:36'],
            'tenantId' => ['sometimes', 'nullable', 'string', 'max:36'],
            'userId' => ['sometimes', 'nullable', 'string', 'max:36'],
            'date' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'time' => ['sometimes', 'string'],
            'appointmentType' => ['sometimes', 'string', 'max:255'],
        ];
    }
}

