<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureTenantSubscriptionIsActive;
use App\Http\Middleware\InitializeTenancyByUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => InitializeTenancyByDomain::class,
            'tenant.by.user' => InitializeTenancyByUser::class,
            'subscription.access' => EnsureTenantSubscriptionIsActive::class,
            'prevent-access-from-central-domains' => PreventAccessFromCentralDomains::class,
        ]);

        $middleware->removeFromGroup('web', [
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
            'tenant',
            'prevent-access-from-central-domains',
        ]);

        $middleware->removeFromGroup('api', [
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
            'tenant',
            'prevent-access-from-central-domains',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
