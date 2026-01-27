<?php

declare(strict_types=1);

namespace App\Http\Requests\DentalLabRequests;

use Illuminate\Foundation\Http\FormRequest;

final class DentalLabStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
