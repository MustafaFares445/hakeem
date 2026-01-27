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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phoneNumber' => $this->phone_number,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'city' => $this->city,
            'streetAddress' => $this->street_address,
            'registrationDate' => $this->registration_date,
            'notes' => $this->notes,
            'primaryImage' => MediaResource::make($this->whenLoaded('media', fn () => $this->getFirstMedia('primary-image'))),
            'lastAppointment' => BookingResource::make($this->whenLoaded('lastAppointment')),
            'firstAppointment' => BookingResource::make($this->whenLoaded('firstAppointment')),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
