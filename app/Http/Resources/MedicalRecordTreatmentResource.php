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
            'id' => $this->id,
            'medicalRecordId' => $this->medical_record_id,
            'treatmentId' => $this->treatment_id,
            'treatment' => TreatmentResource::make($this->whenLoaded('treatment')),
            'treatmentDate' => $this->treatment_date,
            'treatmentCost' => $this->treatment_cost,
            'treatmentDescription' => $this->treatment_description,
            'toothPosition' => $this->tooth_position,
            'fillerMaterialId' => $this->filler_material_id,
            'fillerMaterial' => FillerMaterialResource::make($this->whenLoaded('fillerMaterial')),
            'dentalLabId' => $this->dental_lab_id,
            'dentalLab' => DentalLabResource::make($this->whenLoaded('dentalLab')),
            'sessionNumber' => $this->session_number,
            'doctors' => UserResource::collection($this->whenLoaded('doctors')),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
