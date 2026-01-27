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
            'patientId' => $this->id,
            'patientName' => $this->name,
            'age' => $age,
            'toothType' => $toothType,
            'medicalRecords' => MedicalRecordResource::collection(
                $this->whenLoaded('medicalRecords', fn () => $this->medicalRecords)
            ),
        ];
    }
}
