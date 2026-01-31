<?php

declare(strict_types=1);

namespace App\Http\Requests\BillingRequests;

use Illuminate\Foundation\Http\FormRequest;

final class BillingFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'filter.type' => 'sometimes|nullable|string|max:255',
            'filter.patientId' => 'sometimes|nullable|string|max:255',
            'filter.userId' => 'sometimes|nullable|string|max:255',
            'filter.medicalRecordId' => 'sometimes|nullable|string|max:255',
            'filter.tenantId' => 'sometimes|nullable|string|max:255',
            'filter.date' => 'sometimes|date',
            'filter.createdAfter' => 'sometimes|date',
            'filter.createdBefore' => 'sometimes|date|after_or_equal:filter.createdAfter',
            'filter.search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:type,-type,patientId,-patientId,userId,-userId,medicalRecordId,-medicalRecordId,tenantId,-tenantId,date,-date,createdAt,-createdAt',
        ];
    }

    protected function prepareForValidation(): void
    {
        $filters = $this->input('filter', []);

        if (isset($filters['patientId']) && $filters['patientId'] === '') {
            $filters['patientId'] = null;
        }
        if (isset($filters['userId']) && $filters['userId'] === '') {
            $filters['userId'] = null;
        }
        if (isset($filters['medicalRecordId']) && $filters['medicalRecordId'] === '') {
            $filters['medicalRecordId'] = null;
        }
        if (isset($filters['tenantId']) && $filters['tenantId'] === '') {
            $filters['tenantId'] = null;
        }

        $this->merge(['filter' => $filters]);
    }
}
