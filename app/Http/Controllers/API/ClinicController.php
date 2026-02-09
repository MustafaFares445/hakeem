<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\Clinic\ClinicUpdateRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use App\Services\ClinicService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Throwable;

final readonly class ClinicController
{
    use AuthorizesRequests;

    public function __construct(private ClinicService $clinicService) {}

    /**
     * Return current tenant (clinic) information for the authenticated user.
     */
    public function show(): TenantResource
    {
        /** @var Tenant $tenant */
        $tenant = tenant();

        return TenantResource::make($tenant->load('tenantType'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update current tenant (clinic) data. Number of doctors and secretaries
     * are calculated automatically from users assigned those roles.
     *
     * @throws Throwable
     */
    public function update(ClinicUpdateRequest $request): TenantResource
    {
        /** @var Tenant $tenant */
        $tenant = tenant();
        $tenant = $this->clinicService->update($request->validated(), $tenant);

        return TenantResource::make($tenant->fresh()->load('tenantType'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }
}
