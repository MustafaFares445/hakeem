<?php

declare(strict_types=1);

namespace App\Http\Requests\SubscriptionRequests;

use App\Models\Tenant;
use App\Rules\NoActiveLifetimeSubscriptionRule;
use App\Rules\NoPendingSubscriptionOrderRule;
use App\Rules\SubscriptionPlanHasValidDurationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SubscriptionOrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Tenant|null $tenant */
        $tenant = tenant();
        $tenantTypeId = $tenant?->tenant_type_id;

        return [
            'subscriptionPlanId' => [
                'required',
                'uuid',
                Rule::exists('subscription_plans', 'id')->where(function ($query) use ($tenantTypeId): void {
                    $query->where('is_active', true);

                    if ($tenantTypeId !== null) {
                        $query->where('tenant_type_id', $tenantTypeId);
                    }
                }),
                new SubscriptionPlanHasValidDurationRule(),
                new NoPendingSubscriptionOrderRule(),
                new NoActiveLifetimeSubscriptionRule(),
            ],
            'transactionImage' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
