<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\SubscriptionOrderData;
use App\Http\Requests\SubscriptionRequests\SubscriptionOrderCancelRequest;
use App\Http\Requests\SubscriptionRequests\SubscriptionOrderFilterRequest;
use App\Http\Requests\SubscriptionRequests\SubscriptionOrderStoreRequest;
use App\Http\Resources\SubscriptionOrderResource;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionOrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class SubscriptionOrderController
{
    use AuthorizesRequests;

    public function __construct(private SubscriptionOrderService $subscriptionOrderService) {}

    /**
     * @return AnonymousResourceCollection<SubscriptionOrderResource>
     */
    public function index(SubscriptionOrderFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', SubscriptionOrder::class);

        $orders = SubscriptionOrder::getQuery()
            ->with('media')
            ->paginate($request->integer('perPage', 20));

        return SubscriptionOrderResource::collection($orders)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function store(SubscriptionOrderStoreRequest $request): JsonResponse
    {
        $this->authorize('create', SubscriptionOrder::class);

        $order = $this->subscriptionOrderService->store(
            data: SubscriptionOrderData::from($request->validated()),
            plan: SubscriptionPlan::query()->findOrFail($request->string('subscriptionPlanId')->value())
        );

        return SubscriptionOrderResource::make($order)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws Throwable
     */
    public function update(SubscriptionOrderCancelRequest $request, SubscriptionOrder $subscriptionOrder): SubscriptionOrderResource
    {
        $this->authorize('update', $subscriptionOrder);

        $cancelledOrder = $this->subscriptionOrderService->update($subscriptionOrder);

        return SubscriptionOrderResource::make($cancelledOrder)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }
}
