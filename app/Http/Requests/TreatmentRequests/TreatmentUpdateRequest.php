<?php

declare(strict_types=1);

namespace App\Http\Requests\TreatmentRequests;

use Illuminate\Foundation\Http\FormRequest;

final class TreatmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'defaultCost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }
}
