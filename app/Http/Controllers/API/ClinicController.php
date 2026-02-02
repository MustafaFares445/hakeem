<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\Clinic\ClinicUpdateRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\MessageTrait;

final class ClinicController
{
    use MessageTrait;

    /**
     * Return current tenant (clinic) information for the authenticated user.
     */
    public function show(): TenantResource
    {
        return TenantResource::make(tenant())->additional(['message' => __('Clinic fetched successfully')]);
    }

    /**
     * Update current tenant (clinic) data. Number of doctors and secretaries
     * are calculated automatically from users assigned those roles.
     */
    public function update(ClinicUpdateRequest $request): TenantResource
    {
        /** @var Tenant $tenant */
        $tenant = tenant();

        $validated = $request->validated();
        // Map incoming camelCase API fields to snake_case DB columns
        $attrs = [];

        if (isset($validated['clinicName'])) {
            $attrs['name'] = $validated['clinicName'];
        }
        if (isset($validated['phoneNumber'])) {
            $attrs['phone_number'] = $validated['phoneNumber'];
        }
        if (isset($validated['phoneNumber2'])) {
            $attrs['phone_number2'] = $validated['phoneNumber2'];
        }
        if (isset($validated['specialties'])) {
            $attrs['specialties'] = $validated['specialties'];
        }
        if (isset($validated['mapPin'])) {
            $attrs['map_pin'] = $validated['mapPin'];
        }
        if (isset($validated['city'])) {
            $attrs['city'] = $validated['city'];
        }
        if (isset($validated['address'])) {
            $attrs['address'] = $validated['address'];
        }
        if (isset($validated['instagram'])) {
            $attrs['instagram'] = $validated['instagram'];
        }
        if (isset($validated['facebook'])) {
            $attrs['facebook'] = $validated['facebook'];
        }
        if (isset($validated['startWorkingDay'])) {
            $attrs['start_working_day'] = $validated['startWorkingDay'];
        }
        if (isset($validated['endWorkingDay'])) {
            $attrs['end_working_day'] = $validated['endWorkingDay'];
        }
        if (isset($validated['startWorkingTime'])) {
            $attrs['start_working_time'] = $validated['startWorkingTime'];
        }
        if (isset($validated['endWorkingTime'])) {
            $attrs['end_working_time'] = $validated['endWorkingTime'];
        }

        $tenantId = $tenant->id;

        // Calculate counts scoped to the tenant and set explicit columns
        $doctorsCount = User::role('doctor')->where('tenant_id', $tenantId)->count();
        $secretariesCount = User::role('secretary')->where('tenant_id', $tenantId)->count();

        $attrs['number_of_doctors'] = $doctorsCount;
        $attrs['number_of_secretaries'] = $secretariesCount;

        // Update explicit columns; keep `data` JSON intact for extra values
        $tenant->update($attrs);

        return TenantResource::make($tenant->fresh())->additional(['message' => __('Clinic updated successfully')]);
    }
}
