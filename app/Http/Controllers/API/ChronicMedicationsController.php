<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\ChronicMedicationsData;
use App\Http\Requests\ChronicMedicationsRequests\ChronicMedicationsFilterRequest;
use App\Http\Requests\ChronicMedicationsRequests\ChronicMedicationsStoreRequest;
use App\Http\Requests\ChronicMedicationsRequests\ChronicMedicationsUpdateRequest;
use App\Http\Resources\ChronicMedicationsResource;
use App\Models\ChronicMedications;
use App\Services\ChronicMedicationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class ChronicMedicationsController
{
    public function __construct(private ChronicMedicationsService $chronicMedicationsService) {}

    /**
     * Get a paginated list of chronic_medications with optional filtering.
     *
     * @return AnonymousResourceCollection<ChronicMedications>
     */
    public function index(ChronicMedicationsFilterRequest $request): AnonymousResourceCollection
    {
        $chronicMedications = ChronicMedications::getQuery()
            ->paginate($request->input('perPage', 20));

        return ChronicMedicationsResource::collection($chronicMedications)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new chronicMedications.
     *
     * @throws Throwable
     */
    public function store(ChronicMedicationsStoreRequest $request): JsonResponse
    {
        $chronicMedications = $this->chronicMedicationsService->store(ChronicMedicationsData::from($request->all()));

        return ChronicMedicationsResource::make($chronicMedications)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific chronicMedications by ID.
     */
    public function show(ChronicMedications $chronicMedication): ChronicMedicationsResource
    {
        return ChronicMedicationsResource::make($chronicMedication)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing chronicMedications.
     *
     * @throws Throwable
     */
    public function update(ChronicMedicationsUpdateRequest $request, ChronicMedications $chronicMedication): ChronicMedicationsResource
    {
        $updatedChronicMedications = $this->chronicMedicationsService->update(ChronicMedicationsData::from($request->all()), $chronicMedication);

        return ChronicMedicationsResource::make($updatedChronicMedications)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a chronicMedications.
     */
    public function destroy(ChronicMedications $chronicMedication): ChronicMedicationsResource
    {
        $chronicMedication->delete();

        return ChronicMedicationsResource::make($chronicMedication)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
