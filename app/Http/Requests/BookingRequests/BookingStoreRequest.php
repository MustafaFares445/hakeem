<?php

declare(strict_types=1);

namespace App\Http\Requests\BookingRequests;

use Illuminate\Foundation\Http\FormRequest;

final class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patientId' => ['nullable', 'string', 'max:36'],
            'tenantId' => ['nullable', 'string', 'max:36'],
            'userId' => ['nullable', 'string', 'max:36'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'time' => ['required', 'string'],
            'appointmentType' => ['required', 'string', 'max:255'],
        ];
    }
}

