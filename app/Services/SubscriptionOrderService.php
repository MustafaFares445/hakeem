<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\SubscriptionOrderData;
use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mrmarchone\LaravelAutoCrud\Helpers\MediaHelper;
use Throwable;

final class SubscriptionOrderService
{
    /**
     * @throws Throwable
     */
    public function store(SubscriptionOrderData $data, SubscriptionPlan $plan): SubscriptionOrder
    {
        return DB::transaction(static function () use ($data, $plan): SubscriptionOrder {
            $order = SubscriptionOrder::query()->create([
                'subscription_plan_id' => $data->subscriptionPlanId,
                'created_by_user_id' => Auth::id(),
                'status' => SubscriptionOrderStatusEnum::Pending->value,
                'plan_name' => $plan->name,
                'plan_description' => $plan->description,
                'original_price' => $plan->original_price,
                'price' => $plan->price,
                'currency_code' => $plan->currency_code,
                'duration_value' => $plan->duration_value,
                'duration_unit' => $plan->duration_unit?->value,
                'is_lifetime' => $plan->is_lifetime,
            ]);

            MediaHelper::uploadMedia($data->transactionImage, $order, 'transaction-proof');

            return $order->load('media');
        });
    }

    /**
     * @throws Throwable
     */
    public function update(SubscriptionOrder $order): SubscriptionOrder
    {
        return DB::transaction(static function () use ($order): SubscriptionOrder {
            tap($order)->update([
                'status' => SubscriptionOrderStatusEnum::Cancelled->value,
                'cancelled_at' => now(),
            ]);

            return $order->fresh();
        });
    }
}
