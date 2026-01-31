<?php

declare(strict_types=1);

namespace App\Http\Requests\BillingRequests;

use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class BillingStoreRequest extends FormRequest
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
            'type' => ['required', new Enum(BillingTypeEnum::class)],
            /** @example "2025-01-31" */
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'tenantId' => ['nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => ['nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'userId' => ['nullable', 'string', 'max:36'],
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'medicalRecordId' => ['nullable', 'string', 'max:36'],
            /** @example "Consultation" */
            'caseName' => [Rule::requiredIf($typeIncoming), 'nullable', 'string', 'max:255'],
            /** @example 150.00 */
            'paidAmount' => [Rule::requiredIf($typeIncoming), 'nullable', 'numeric', 'min:0'],
            /** @example 200.00 */
            'totalCost' => [Rule::requiredIf($typeIncoming), 'nullable', 'numeric', 'min:0'],
            /** @example "Lab work" */
            'itemName' => [Rule::requiredIf($typeOutgoing), 'nullable', 'string', 'max:255'],
            /** @example 1 */
            'quantity' => [Rule::requiredIf($typeOutgoing), 'nullable', 'integer', 'min:0'],
            /** @example 50.00 */
            'amount' => [Rule::requiredIf($typeOutgoing), 'nullable', 'numeric', 'min:0'],
            /** @example "supplies" */
            'outgoingType' => [Rule::requiredIf($typeOutgoing), 'nullable', new Enum(BillingOutgoingTypeEnum::class)],
        ];
    }
}
