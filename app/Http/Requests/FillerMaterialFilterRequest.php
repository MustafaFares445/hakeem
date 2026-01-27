<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class FillerMaterialFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'filter.name' => ['nullable', 'string'],
            'filter.description' => ['nullable', 'string'],
            'filter.isActive' => ['nullable', 'boolean'],
            'filter.createdAfter' => ['nullable', 'date'],
            'filter.createdBefore' => ['nullable', 'date'],
            'filter.search' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
