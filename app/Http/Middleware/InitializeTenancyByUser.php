<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Scopes\MainTenantScope;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class InitializeTenancyByUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $tenantId = $user->tenant_id;

        if ($tenantId === null) {
            return $next($request);
        }

        $tenant = Tenant::withoutGlobalScope(MainTenantScope::class)->find($tenantId);

        if ($tenant === null) {
            throw new AccessDeniedHttpException(__('Tenant not found for this user.'));
        }

        tenancy()->initialize($tenant);

        return $next($request);
    }
}
