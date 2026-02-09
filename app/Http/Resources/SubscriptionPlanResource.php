<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SubscriptionPlan
 */
final class SubscriptionPlanResource extends JsonResource
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
            'tenantTypeId' => $this->tenant_type_id,
            'name' => $this->name,
            'description' => $this->description,
            'originalPrice' => (float) $this->original_price,
            'price' => (float) $this->price,
            'currencyCode' => $this->currency_code,
            'durationValue' => $this->duration_value,
            'durationUnit' => $this->duration_unit?->value,
            'isLifetime' => (bool) $this->is_lifetime,
            'isActive' => (bool) $this->is_active,
            'sortOrder' => (int) $this->sort_order,
            'createdAt' => $this->created_at?->toDateTimeString(),
            'updatedAt' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
