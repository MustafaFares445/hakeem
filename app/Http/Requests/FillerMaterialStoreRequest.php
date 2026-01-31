<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class FillerMaterialStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /** @example "Composite A2" */
            'name' => ['required', 'string', 'max:255'],
            /** @example "Dental composite material" */
            'description' => ['nullable', 'string', 'max:1000'],
            /** @example true */
            'isActive' => ['nullable', 'boolean'],
        ];
    }
}
