<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\SubscriptionStatusResource;
use App\Models\Tenant;
use App\Services\SubscriptionAccessService;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

final readonly class SubscriptionStatusController
{
    public function __construct(private SubscriptionAccessService $subscriptionAccessService) {}

    public function show(): SubscriptionStatusResource
    {
        /** @var Tenant|null $tenant */
        $tenant = tenant();
        $status = $this->subscriptionAccessService->forTenant($tenant);

        return SubscriptionStatusResource::make($status)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
