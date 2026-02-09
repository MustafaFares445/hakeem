<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\SubscriptionAccessResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SubscriptionAccessResult
 */
final class SubscriptionStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'canUseApp' => $this->canUseApp,
            'reason' => $this->reason->value,
            'trialEndsAt' => $this->trialEndsAt?->toDateTimeString(),
            'activeUntil' => $this->activeUntil?->toDateTimeString(),
            'hasPendingOrder' => $this->hasPendingOrder,
        ];
    }
}
