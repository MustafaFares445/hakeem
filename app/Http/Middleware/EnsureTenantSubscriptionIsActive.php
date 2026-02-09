<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\SubscriptionAccessService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureTenantSubscriptionIsActive
{
    public function __construct(private readonly SubscriptionAccessService $subscriptionAccessService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->tenant_id === null) {
            return $next($request);
        }

        /** @var Tenant|null $tenant */
        $tenant = tenant();
        $status = $this->subscriptionAccessService->forTenant($tenant);

        if ($status->canUseApp) {
            return $next($request);
        }

        return new JsonResponse([
            'message' => __('Subscription is inactive. Please renew your plan.'),
            'subscriptionStatus' => [
                'canUseApp' => $status->canUseApp,
                'reason' => $status->reason->value,
                'trialEndsAt' => $status->trialEndsAt?->toIso8601String(),
                'activeUntil' => $status->activeUntil?->toIso8601String(),
                'hasPendingOrder' => $status->hasPendingOrder,
            ],
        ], Response::HTTP_PAYMENT_REQUIRED);
    }
}
