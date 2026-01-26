<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ChronicDiseases;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ChronicDiseases
 */
final class ChronicDiseasesResource extends JsonResource
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
            'patientId' => $this->patient_id ?? null,
            'title' => $this->title ?? null,
            'createdAt' => $this->created_at?->toDateTimeString(),
            'updatedAt' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
