<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class PendingSubscriptionOrderStatusRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $isPending = SubscriptionOrder::query()
            ->where('id', $value)
            ->where('status', SubscriptionOrderStatusEnum::Pending->value)
            ->exists();

        if (! $isPending) {
            $fail(__('Only pending orders can be cancelled.'));
        }
    }
}
