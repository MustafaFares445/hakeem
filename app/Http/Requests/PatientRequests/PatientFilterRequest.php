<?php

declare(strict_types=1);

namespace App\Http\Requests\PatientRequests;

use Illuminate\Foundation\Http\FormRequest;

final class PatientFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'filter.name' => 'sometimes|string|max:255',
            'filter.email' => 'sometimes|email|max:255',
            'filter.phoneNumber' => 'sometimes|string|max:255',
            'filter.birthday' => 'sometimes|date',
            'filter.gender' => 'sometimes|string|max:255',
            'filter.city' => 'sometimes|string|max:255',
            'filter.streetAddress' => 'sometimes|string|max:255',
            'filter.registrationDate' => 'sometimes|date',
            'filter.notes' => 'sometimes|string|max:255',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
            'filter.search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:name,-name,email,-email,phoneNumber,-phoneNumber,birthday,-birthday,gender,-gender,city,-city,streetAddress,-streetAddress,registrationDate,-registrationDate,notes,-notes',
        ];
    }
}
