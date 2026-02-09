<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**@mixin Tenant*/
final class TenantResource extends JsonResource
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
            /** @example "Clinic A" */
            'name' => $this->name,
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'tenantTypeId' => $this->tenant_type_id,
            'tenantType' => TenantTypeResource::make($this->whenLoaded('tenantType')),
            'data' => $this->data,
            'phoneNumber' => $this->phone_number,
            'phoneNumber2' => $this->phone_number2,
            'specialties' => $this->specialties,
            'numberOfDoctors' => $this->number_of_doctors,
            'numberOfSecretaries' => $this->number_of_secretaries,
            'mapPin' => $this->map_pin,
            'city' => $this->city,
            'address' => $this->address,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
            'startWorkingDay' => $this->start_working_day,
            'endWorkingDay' => $this->end_working_day,
            'startWorkingTime' => $this->start_working_time,
            'endWorkingTime' => $this->end_working_time,
            'trialStartsAt' => $this->trial_starts_at?->toDateTimeString(),
            'trialEndsAt' => $this->trial_ends_at?->toDateTimeString(),
            /** @example "clinic-a.example.com" */
            'domainName' => $this->domain_name,
            /** @example "2025-01-01 12:00:00" */
            'createdAt' => $this->created_at?->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
