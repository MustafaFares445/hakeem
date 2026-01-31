<?php

declare(strict_types=1);

namespace App\Http\Requests\DentalLabRequests;

use Illuminate\Foundation\Http\FormRequest;

final class DentalLabStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Al-Noor Dental Lab" */
            'name' => ['required', 'string', 'max:255'],
            /** @example "+966501234567" */
            'phone' => ['nullable', 'string', 'max:20'],
            /** @example "Industrial Area, Riyadh" */
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
