<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MedicalRecordTreatment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MedicalRecordTreatment
 */
final class MedicalRecordTreatmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'id' => $this->id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'medicalRecordId' => $this->medical_record_id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'treatmentId' => $this->treatment_id,
            'treatment' => TreatmentResource::make($this->whenLoaded('treatment')),
            /** @example "2025-01-31" */
            'treatmentDate' => $this->treatment_date,
            /** @example 100.00 */
            'treatmentCost' => $this->treatment_cost,
            /** @example "Completed successfully" */
            'treatmentDescription' => $this->treatment_description,
            /** @example "upper_left_molar" */
            'toothPosition' => $this->tooth_position,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'fillerMaterialId' => $this->filler_material_id,
            'fillerMaterial' => FillerMaterialResource::make($this->whenLoaded('fillerMaterial')),
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'dentalLabId' => $this->dental_lab_id,
            'dentalLab' => DentalLabResource::make($this->whenLoaded('dentalLab')),
            /** @example 1 */
            'sessionNumber' => $this->session_number,
            'doctors' => UserResource::collection($this->whenLoaded('doctors')),
            /** @example "2025-01-31 10:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
