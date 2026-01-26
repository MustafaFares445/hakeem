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
            'patientId' => ['required', 'string', 'exists:patients,id'],
            'title' => ['required', 'string', 'max:255'],
        ];
    }
}
