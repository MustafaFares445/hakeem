<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class NoActiveLifetimeSubscriptionRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasLifetimeAccess = SubscriptionOrder::query()
            ->where('status', SubscriptionOrderStatusEnum::Confirmed->value)
            ->where('is_lifetime', true)
            ->where(function ($query): void {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->exists();

        if ($hasLifetimeAccess) {
            $fail(__('This tenant already has an active lifetime subscription.'));
        }
    }
}
