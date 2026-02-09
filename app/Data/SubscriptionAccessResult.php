<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\SubscriptionAccessReasonEnum;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

final class SubscriptionAccessResult extends Data
{
    public function __construct(
        public bool $canUseApp,
        public SubscriptionAccessReasonEnum $reason,
        public ?CarbonImmutable $trialEndsAt,
        public ?CarbonImmutable $activeUntil,
        public bool $hasPendingOrder,
    ) {}
}
