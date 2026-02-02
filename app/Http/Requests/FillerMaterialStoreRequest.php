<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FillerMaterialColorEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            /** @example "yellow" */
            'color' => ['nullable', 'string', Rule::enum(FillerMaterialColorEnum::class)],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'dentalLabId' => ['nullable', 'uuid', Rule::exists('dental_labs', 'id')],
            /** @example "Dental composite material" */
            'description' => ['nullable', 'string', 'max:1000'],
            /** @example true */
            'isActive' => ['nullable', 'boolean'],
        ];
    }
}
