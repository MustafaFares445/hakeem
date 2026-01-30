<?php

declare(strict_types=1);

namespace App\Http\Requests\BookingRequests;

use Illuminate\Foundation\Http\FormRequest;

class BookingFilterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'search'  => 'sometimes|string|max:255',
            'filter.patientId' => 'sometimes|nullable|string|max:255',
            'filter.tenantId' => 'sometimes|nullable|string|max:255',
            'filter.userId' => 'sometimes|nullable|string|max:255',
            'filter.date' => 'sometimes|date',
            'filter.time' => 'sometimes|string|max:255',
            'filter.appointmentType' => 'sometimes|string|max:255',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
            'filter.search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:patientId,-patientId,tenantId,-tenantId,userId,-userId,date,-date,time,-time,appointmentType,-appointmentType',
        ];
    }

    protected function prepareForValidation(): void
    {
        $filters = $this->input('filter', []);

        if (isset($filters['patientId']) && $filters['patientId'] === '') {
            $filters['patientId'] = null;
        }
        if (isset($filters['tenantId']) && $filters['tenantId'] === '') {
            $filters['tenantId'] = null;
        }
        if (isset($filters['userId']) && $filters['userId'] === '') {
            $filters['userId'] = null;
        }

        $this->merge(['filter' => $filters]);
    }
}
