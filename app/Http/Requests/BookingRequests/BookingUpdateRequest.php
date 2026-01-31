<?php

declare(strict_types=1);

namespace App\Http\Requests\BookingRequests;

use App\Enums\AppointmentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class BookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => ['sometimes', 'nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'tenantId' => ['sometimes', 'nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'userId' => ['sometimes', 'nullable', 'string', 'max:36'],
            /** @example "2025-02-01" */
            'date' => ['sometimes', 'date', 'date_format:Y-m-d'],
            /** @example "09:00" */
            'time' => ['sometimes', 'string'],
            /** @example "preview" */
            'appointmentType' => ['sometimes', new Enum(AppointmentTypeEnum::class)],
        ];
    }
}
