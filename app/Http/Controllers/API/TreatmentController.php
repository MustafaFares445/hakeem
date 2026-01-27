<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\TreatmentData;
use App\Http\Requests\TreatmentRequests\TreatmentFilterRequest;
use App\Http\Requests\TreatmentRequests\TreatmentStoreRequest;
use App\Http\Requests\TreatmentRequests\TreatmentUpdateRequest;
use App\Http\Resources\TreatmentResource;
use App\Models\Treatment;
use App\Services\TreatmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class TreatmentController
{
    use AuthorizesRequests;

    public function __construct(private TreatmentService $treatmentService) {}

    /**
     * Get a paginated list of treatments with optional filtering.
     *
     * @return AnonymousResourceCollection<TreatmentResource>
     */
    public function index(TreatmentFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Treatment::class);

        $treatments = Treatment::getQuery()
            ->paginate($request->input('perPage', 20));

        return TreatmentResource::collection($treatments)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new treatment.
     *
     * @throws Throwable
     */
    public function store(TreatmentStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Treatment::class);

        $treatment = $this->treatmentService->store(TreatmentData::from($request->validated()));

        return TreatmentResource::make($treatment)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific treatment by ID.
     */
    public function show(Treatment $treatment): TreatmentResource
    {
        $this->authorize('view', $treatment);

        return TreatmentResource::make($treatment)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing treatment.
     *
     * @throws Throwable
     */
    public function update(TreatmentUpdateRequest $request, Treatment $treatment): TreatmentResource
    {
        $this->authorize('update', $treatment);

        $updatedTreatment = $this->treatmentService->update(TreatmentData::from($request->validated()), $treatment);

        return TreatmentResource::make($updatedTreatment)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a treatment.
     */
    public function destroy(Treatment $treatment): TreatmentResource
    {
        $this->authorize('delete', $treatment);

        $treatment->delete();

        return TreatmentResource::make($treatment)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
