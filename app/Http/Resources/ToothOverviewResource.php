<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Patient
 */
final class ToothOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $age = $this->birthday?->age ?? 18;
        $toothType = $age < 12 ? 'primary' : 'permanent';

        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => $this->id,
            /** @example "Omar Hassan" */
            'patientName' => $this->name,
            /** @example 34 */
            'age' => $age,
            /** @example "permanent" */
            'toothType' => $toothType,
            'medicalRecords' => MedicalRecordResource::collection(
                $this->whenLoaded('medicalRecords', fn () => $this->medicalRecords)
            ),
        ];
    }
}
