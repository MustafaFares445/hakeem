<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Treatment
 */
final class TreatmentResource extends JsonResource
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
            /** @example "Root Canal" */
            'name' => $this->name,
            /** @example "Standard root canal procedure" */
            'description' => $this->description,
            /** @example 150.00 */
            'defaultCost' => $this->default_cost,
            /** @example "2025-01-01 12:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
