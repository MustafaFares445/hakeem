<?php

declare(strict_types=1);

namespace App\Http\Requests\ChronicDiseasesRequests;

use Illuminate\Foundation\Http\FormRequest;

final class ChronicDiseasesUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patientId' => ['sometimes', 'required', 'string', 'exists:patients,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
