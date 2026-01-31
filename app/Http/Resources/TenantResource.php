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
            /** @example "clinic" */
            'type' => $this->type,
            'data' => $this->data,
            /** @example "clinic-a.example.com" */
            'domainName' => $this->domain_name,
            /** @example "2025-01-01 12:00:00" */
            'createdAt' => $this->created_at?->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
