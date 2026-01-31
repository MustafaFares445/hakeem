<?php

declare(strict_types=1);

namespace App\Http\Requests\ChronicDiseasesRequests;

use Illuminate\Foundation\Http\FormRequest;

final class ChronicDiseasesStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => ['required', 'string', 'exists:patients,id'],
            /** @example "Diabetes" */
            'title' => ['required', 'string', 'max:255'],
        ];
    }
}
