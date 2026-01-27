<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\FillerMaterialData;
use App\Http\Requests\FillerMaterialFilterRequest;
use App\Http\Requests\FillerMaterialStoreRequest;
use App\Http\Requests\FillerMaterialUpdateRequest;
use App\Http\Resources\FillerMaterialResource;
use App\Models\FillerMaterial;
use App\Services\FillerMaterialService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class FillerMaterialController
{
    use AuthorizesRequests;

    public function __construct(private readonly FillerMaterialService $service) {}

    /**
     * @return AnonymousResourceCollection<FillerMaterialResource>
     */
    public function index(FillerMaterialFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', FillerMaterial::class);

        $fillerMaterials = FillerMaterial::getQuery()
            ->paginate($request->input('perPage', 20));

        return FillerMaterialResource::collection($fillerMaterials)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function store(FillerMaterialStoreRequest $request)
    {
        $this->authorize('create', FillerMaterial::class);

        $fillerMaterial = $this->service->store(FillerMaterialData::from($request->validated()));

        return FillerMaterialResource::make($fillerMaterial)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     */
    public function show(FillerMaterial $fillerMaterial): FillerMaterialResource
    {
        $this->authorize('view', $fillerMaterial);

        return FillerMaterialResource::make($fillerMaterial)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     */
    public function update(FillerMaterialUpdateRequest $request, FillerMaterial $fillerMaterial): FillerMaterialResource
    {
        $this->authorize('update', $fillerMaterial);

        $data = FillerMaterialData::from($request->validated());

        $updated = $this->service->update($data, $fillerMaterial);

        return FillerMaterialResource::make($updated)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     */
    public function destroy(FillerMaterial $fillerMaterial): FillerMaterialResource
    {
        $this->authorize('delete', $fillerMaterial);

        $this->service->delete($fillerMaterial);

        return FillerMaterialResource::make($fillerMaterial)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
