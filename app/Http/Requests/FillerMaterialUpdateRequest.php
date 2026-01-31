<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class FillerMaterialUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Composite A2" */
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            /** @example "Dental composite material" */
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            /** @example true */
            'isActive' => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
