<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\TenantTypeResource;
use App\Models\TenantType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;

final readonly class TenantTypeController
{
    /**
     * @return AnonymousResourceCollection<TenantTypeResource>
     */
    public function index(): AnonymousResourceCollection
    {
        $tenantTypes = TenantType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return TenantTypeResource::collection($tenantTypes)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
