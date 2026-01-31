<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Billing
 */
final class BillingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'id' => $this->id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'tenantId' => $this->tenant_id,
            /** @example "incoming" */
            'type' => $this->type?->value,
            /** @example "2025-01-31" */
            'date' => $this->date?->toDateString(),
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => $this->patient_id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'userId' => $this->user_id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'medicalRecordId' => $this->medical_record_id,
            /** @example "Consultation" */
            'caseName' => $this->case_name,
            /** @example 150.00 */
            'paidAmount' => $this->paid_amount !== null ? (float) $this->paid_amount : null,
            /** @example 200.00 */
            'totalCost' => $this->total_cost !== null ? (float) $this->total_cost : null,
            /** @example "Lab work" */
            'itemName' => $this->item_name,
            /** @example 1 */
            'quantity' => $this->quantity,
            /** @example 50.00 */
            'amount' => $this->amount !== null ? (float) $this->amount : null,
            /** @example "supplies" */
            'outgoingType' => $this->outgoing_type?->value,
            /** @example "2025-01-31 10:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
