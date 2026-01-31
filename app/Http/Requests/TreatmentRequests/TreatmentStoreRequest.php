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
            /** @example "Root Canal" */
            'name' => ['required', 'string', 'max:255'],
            /** @example "Standard root canal procedure" */
            'description' => ['nullable', 'string', 'max:1000'],
            /** @example 150.00 */
            'defaultCost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
