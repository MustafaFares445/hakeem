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
        return [
            'id' => $this->id,
            'patientId' => $this->patient_id,
            'patient' => PatientResource::make($this->whenLoaded('patient')),
            'recordDate' => $this->record_date?->toDateString(),
            'recordType' => $this->record_type,
            'caseName' => $this->case_name,
            'description' => $this->description,
            'totalCost' => $this->total_cost,
            'remainingAmount' => $this->remaining_amount,
            'treatments' => MedicalRecordTreatmentResource::collection($this->whenLoaded('treatments')),
            'attachments' => MediaResource::collection($this->whenLoaded('media')),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
