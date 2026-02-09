<?php

declare(strict_types=1);

namespace App\Http\Requests\SubscriptionRequests;

use App\Models\Tenant;
use App\Rules\PendingSubscriptionOrderStatusRule;
use App\Rules\SubscriptionOrderBelongsToTenantRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SubscriptionOrderCancelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var Tenant|null $tenant */
        $tenant = tenant();

        return [
            'subscriptionOrderId' => [
                'required',
                'uuid',
                Rule::exists('subscription_orders', 'id'),
                new SubscriptionOrderBelongsToTenantRule($tenant?->id),
                new PendingSubscriptionOrderStatusRule(),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $subscriptionOrder = $this->route('subscriptionOrder');
        $subscriptionOrderId = $subscriptionOrder?->id ?? $subscriptionOrder;

        $this->merge([
            'subscriptionOrderId' => $subscriptionOrderId,
        ]);
    }
}
