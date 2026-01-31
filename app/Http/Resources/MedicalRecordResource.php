<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MedicalRecord
 */
final class MedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalCost = 0;
        $amountPaid = 0;
        if ($this->relationLoaded('billings')) {
            $billings = $this->billings;
            $totalCost = (float) $billings->max('total_cost');
            $amountPaid = (float) $billings->sum('paid_amount');
        }
        $remainingAmount = $totalCost - $amountPaid;

        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'id' => $this->id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => $this->patient_id,
            'patient' => PatientResource::make($this->whenLoaded('patient')),
            /** @example "2025-01-31" */
            'recordDate' => $this->record_date?->toDateString(),
            /** @example "in_clinic" */
            'recordType' => $this->record_type,
            /** @example "Initial examination" */
            'caseName' => $this->case_name,
            /** @example "Patient presented with tooth pain" */
            'description' => $this->description,
            /** @example 200.00 */
            'totalCost' => $totalCost > 0 ? $totalCost : null,
            /** @example 150.00 */
            'amountPaid' => $amountPaid > 0 ? $amountPaid : 0,
            /** @example 50.00 */
            'remainingAmount' => $remainingAmount,
            'treatments' => MedicalRecordTreatmentResource::collection($this->whenLoaded('treatments')),
            'attachments' => MediaResource::collection($this->whenLoaded('media')),
            /** @example "2025-01-31 10:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
