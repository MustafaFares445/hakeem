<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class NoPendingSubscriptionOrderRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasPendingOrder = SubscriptionOrder::query()
            ->where('status', SubscriptionOrderStatusEnum::Pending->value)
            ->exists();

        if ($hasPendingOrder) {
            $fail(__('A pending subscription order already exists.'));
        }
    }
}
