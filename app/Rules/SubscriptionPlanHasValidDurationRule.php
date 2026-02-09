<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\SubscriptionPlan;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class SubscriptionPlanHasValidDurationRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plan = SubscriptionPlan::query()->findOrFail($value);

        $hasValidDurationShape = $plan->is_lifetime
            ? $plan->duration_value === null && $plan->duration_unit === null
            : $plan->duration_value !== null && $plan->duration_unit !== null;

        if (! $hasValidDurationShape) {
            $fail(__('Selected plan has invalid duration configuration.'));
        }
    }
}
