<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\DentalLabData;
use App\Http\Requests\DentalLabRequests\DentalLabFilterRequest;
use App\Http\Requests\DentalLabRequests\DentalLabStoreRequest;
use App\Http\Requests\DentalLabRequests\DentalLabUpdateRequest;
use App\Http\Resources\DentalLabResource;
use App\Models\DentalLab;
use App\Services\DentalLabService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class DentalLabController
{
    use AuthorizesRequests;

    public function __construct(private DentalLabService $dentalLabService) {}

    /**
     * Get a paginated list of dental labs with optional filtering.
     *
     * @return AnonymousResourceCollection<DentalLabResource>
     */
    public function index(DentalLabFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', DentalLab::class);

        $dentalLabs = DentalLab::getQuery()
            ->paginate($request->input('perPage', 20));

        return DentalLabResource::collection($dentalLabs)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new dental lab.
     *
     * @throws Throwable
     */
    public function store(DentalLabStoreRequest $request): JsonResponse
    {
        $this->authorize('create', DentalLab::class);

        $dentalLab = $this->dentalLabService->store(DentalLabData::from($request->validated()));

        return DentalLabResource::make($dentalLab)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific dental lab by ID.
     */
    public function show(DentalLab $dentalLab): DentalLabResource
    {
        $this->authorize('view', $dentalLab);

        return DentalLabResource::make($dentalLab)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing dental lab.
     *
     * @throws Throwable
     */
    public function update(DentalLabUpdateRequest $request, DentalLab $dentalLab): DentalLabResource
    {
        $this->authorize('update', $dentalLab);

        $updatedDentalLab = $this->dentalLabService->update(DentalLabData::from($request->validated()), $dentalLab);

        return DentalLabResource::make($updatedDentalLab)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a dental lab.
     */
    public function destroy(DentalLab $dentalLab): DentalLabResource
    {
        $this->authorize('delete', $dentalLab);

        $dentalLab->delete();

        return DentalLabResource::make($dentalLab)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
