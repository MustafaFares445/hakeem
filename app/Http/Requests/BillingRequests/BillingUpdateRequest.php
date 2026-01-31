<?php

declare(strict_types=1);

namespace App\Http\Requests\BillingRequests;

use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class BillingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $typeIncoming = $this->input('type') === BillingTypeEnum::Incoming->value;
        $typeOutgoing = $this->input('type') === BillingTypeEnum::Outgoing->value;

        return [
            /** @example "incoming" */
            'type' => ['sometimes', new Enum(BillingTypeEnum::class)],
            /** @example "2025-01-31" */
            'date' => ['sometimes', 'date', 'date_format:Y-m-d'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'tenantId' => ['sometimes', 'nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => ['sometimes', 'nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'userId' => ['sometimes', 'nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'medicalRecordId' => ['sometimes', 'nullable', 'string', 'max:36'],
            /** @example "Consultation" */
            'caseName' => [Rule::requiredIf($typeIncoming), 'sometimes', 'nullable', 'string', 'max:255'],
            /** @example 200.00 */
            'paidAmount' => [Rule::requiredIf($typeIncoming), 'sometimes', 'nullable', 'numeric', 'min:0'],
            /** @example 250.00 */
            'totalCost' => [Rule::requiredIf($typeIncoming), 'sometimes', 'nullable', 'numeric', 'min:0'],
            /** @example "Lab work" */
            'itemName' => [Rule::requiredIf($typeOutgoing), 'sometimes', 'nullable', 'string', 'max:255'],
            /** @example 2 */
            'quantity' => [Rule::requiredIf($typeOutgoing), 'sometimes', 'nullable', 'integer', 'min:0'],
            /** @example 50.00 */
            'amount' => [Rule::requiredIf($typeOutgoing), 'sometimes', 'nullable', 'numeric', 'min:0'],
            /** @example "supplies" */
            'outgoingType' => [Rule::requiredIf($typeOutgoing), 'sometimes', 'nullable', new Enum(BillingOutgoingTypeEnum::class)],
        ];
    }
}
