<?php

declare(strict_types=1);

namespace App\Http\Requests\TreatmentRequests;

use Illuminate\Foundation\Http\FormRequest;

final class TreatmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'defaultCost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
