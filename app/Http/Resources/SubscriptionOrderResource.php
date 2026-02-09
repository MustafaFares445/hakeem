<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\SubscriptionOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SubscriptionOrder
 */
final class SubscriptionOrderResource extends JsonResource
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
            'tenantId' => $this->tenant_id,
            'subscriptionPlanId' => $this->subscription_plan_id,
            'createdByUserId' => $this->created_by_user_id,
            'status' => $this->status?->value,
            'planName' => $this->plan_name,
            'planDescription' => $this->plan_description,
            'originalPrice' => (float) $this->original_price,
            'price' => (float) $this->price,
            'currencyCode' => $this->currency_code,
            'durationValue' => $this->duration_value,
            'durationUnit' => $this->duration_unit?->value,
            'isLifetime' => (bool) $this->is_lifetime,
            'startsAt' => $this->starts_at?->toDateTimeString(),
            'endsAt' => $this->ends_at?->toDateTimeString(),
            'confirmedAt' => $this->confirmed_at?->toDateTimeString(),
            'cancelledAt' => $this->cancelled_at?->toDateTimeString(),
            'cancellationReason' => $this->cancellation_reason,
            'transactionProof' => MediaResource::make(
                $this->whenLoaded('media', fn () => $this->getFirstMedia('transaction-proof'))
            ),
            'createdAt' => $this->created_at?->toDateTimeString(),
            'updatedAt' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
