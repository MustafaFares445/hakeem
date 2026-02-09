<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\SubscriptionOrder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class SubscriptionOrderBelongsToTenantRule implements ValidationRule
{
    public function __construct(private readonly ?string $tenantId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->tenantId === null || ! is_string($value) || $value === '') {
            return;
        }

        $belongsToTenant = SubscriptionOrder::query()
            ->where('id', $value)
            ->where('tenant_id', $this->tenantId)
            ->exists();

        if (! $belongsToTenant) {
            $fail(__('Subscription order not found for this tenant.'));
        }
    }
}
