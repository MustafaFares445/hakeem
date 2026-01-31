<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\BillingData;
use App\Http\Requests\BillingRequests\BillingFilterRequest;
use App\Http\Requests\BillingRequests\BillingStoreRequest;
use App\Http\Requests\BillingRequests\BillingUpdateRequest;
use App\Http\Resources\BillingResource;
use App\Models\Billing;
use App\Services\BillingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class BillingController
{
    use AuthorizesRequests;

    public function __construct(private BillingService $billingService) {}

    /**
     * @return AnonymousResourceCollection<BillingResource>
     */
    public function index(BillingFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Billing::class);

        $billings = Billing::getQuery()
            ->paginate($request->input('perPage', 20));

        return BillingResource::collection($billings)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function store(BillingStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Billing::class);

        $billing = $this->billingService->store(BillingData::from($request->validated()));

        return BillingResource::make($billing)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Billing $billing): BillingResource
    {
        $this->authorize('view', $billing);

        return BillingResource::make($billing)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function update(BillingUpdateRequest $request, Billing $billing): BillingResource
    {
        $this->authorize('update', $billing);

        $updatedBilling = $this->billingService->update(BillingData::from($request->validated()), $billing);

        return BillingResource::make($updatedBilling)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    public function destroy(Billing $billing): BillingResource
    {
        $this->authorize('delete', $billing);

        $billing->delete();

        return BillingResource::make($billing)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
