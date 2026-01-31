<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\DentalLab;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DentalLab
 */
final class DentalLabResource extends JsonResource
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
            /** @example "Al-Noor Dental Lab" */
            'name' => $this->name,
            /** @example "+966501234567" */
            'phone' => $this->phone,
            /** @example "Industrial Area, Riyadh" */
            'address' => $this->address,
            /** @example "2025-01-01 12:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
