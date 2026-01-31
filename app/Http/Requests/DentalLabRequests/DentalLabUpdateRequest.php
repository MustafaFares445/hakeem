<?php

declare(strict_types=1);

namespace App\Http\Requests\DentalLabRequests;

use Illuminate\Foundation\Http\FormRequest;

final class DentalLabUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Al-Noor Dental Lab" */
            'name' => ['sometimes', 'string', 'max:255'],
            /** @example "+966501234567" */
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            /** @example "Industrial Area, Riyadh" */
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
