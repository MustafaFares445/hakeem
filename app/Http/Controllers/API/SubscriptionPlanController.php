<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

final readonly class SubscriptionPlanController
{
    /**
     * @return AnonymousResourceCollection<SubscriptionPlanResource>
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $tenantTypeId = $request->user()?->tenant?->tenant_type_id;

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->when($tenantTypeId !== null, fn ($query) => $query->where('tenant_type_id', $tenantTypeId))
            ->orderBy('sort_order')
            ->get();

        return SubscriptionPlanResource::collection($plans)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
