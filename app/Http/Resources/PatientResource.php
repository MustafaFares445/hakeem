<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Patient
 */
final class PatientResource extends JsonResource
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
            /** @example "Omar Hassan" */
            'name' => $this->name,
            /** @example "omar@example.com" */
            'email' => $this->email,
            /** @example "+966501234567" */
            'phoneNumber' => $this->phone_number,
            /** @example "1990-05-15" */
            'birthday' => $this->birthday,
            /** @example "male" */
            'gender' => $this->gender,
            /** @example "Riyadh" */
            'city' => $this->city,
            /** @example "King Fahd Road" */
            'streetAddress' => $this->street_address,
            /** @example "2025-01-15" */
            'registrationDate' => $this->registration_date,
            /** @example "Regular checkup" */
            'notes' => $this->notes,
            'primaryImage' => MediaResource::make($this->whenLoaded('media', fn () => $this->getFirstMedia('primary-image'))),
            'lastAppointment' => BookingResource::make($this->whenLoaded('lastAppointment')),
            'firstAppointment' => BookingResource::make($this->whenLoaded('firstAppointment')),
            /** @example "2025-01-15 10:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
