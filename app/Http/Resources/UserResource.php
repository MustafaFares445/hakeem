<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
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
            /** @example "Dr. Ahmed Ali" */
            'name' => $this->name,
            /** @example "ahmed@example.com" */
            'email' => $this->email,
            /** @example "2025-01-01 12:00:00" */
            'emailVerifiedAt' => $this->email_verified_at,
            'primaryImage' => MediaResource::make($this->whenLoaded('media', fn () => $this->getFirstMedia('primary-image'))),
            'tenant' => TenantResource::make($this->whenLoaded('tenant')),
            /** @example "2025-01-01 12:00:00" */
            'createdAt' => $this->created_at->toDateTimeString(),
            /** @example "2025-01-31 12:00:00" */
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
