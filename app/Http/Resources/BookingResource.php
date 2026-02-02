<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Booking
 */
final class BookingResource extends JsonResource
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
            'patient' => PatientResource::make($this->whenLoaded('patient')),
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'user' => UserResource::make($this->whenLoaded('user')),
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'patientId' => $this->patient_id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'tenantId' => $this->tenant_id,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'userId' => $this->user_id,
            /** @example "2025-02-01" */
            'date' => $this->date,
            /** @example "09:00" */
            'time' => $this->time,
            /** @example "preview" */
            'appointmentType' => $this->appointment_type?->value,
            /** @example "2025-01-31 10:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
