<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\ChronicDiseasesData;
use App\Http\Requests\ChronicDiseasesRequests\ChronicDiseasesFilterRequest;
use App\Http\Requests\ChronicDiseasesRequests\ChronicDiseasesStoreRequest;
use App\Http\Requests\ChronicDiseasesRequests\ChronicDiseasesUpdateRequest;
use App\Http\Resources\ChronicDiseasesResource;
use App\Models\ChronicDiseases;
use App\Services\ChronicDiseasesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class ChronicDiseasesController
{
    public function __construct(private ChronicDiseasesService $chronicDiseasesService) {}

    /**
     * Get a paginated list of chronic_diseases with optional filtering.
     *
     * @return AnonymousResourceCollection<ChronicDiseases>
     */
    public function index(ChronicDiseasesFilterRequest $request): AnonymousResourceCollection
    {
        $chronicDiseases = ChronicDiseases::getQuery()
            ->paginate($request->input('perPage', 20));

        return ChronicDiseasesResource::collection($chronicDiseases)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new chronicDiseases.
     *
     * @throws Throwable
     */
    public function store(ChronicDiseasesStoreRequest $request): JsonResponse
    {
        $chronicDiseases = $this->chronicDiseasesService->store(ChronicDiseasesData::from($request->all()));

        return ChronicDiseasesResource::make($chronicDiseases)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific chronicDiseases by ID.
     */
    public function show(ChronicDiseases $chronicDisease): ChronicDiseasesResource
    {
        return ChronicDiseasesResource::make($chronicDisease)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing chronicDiseases.
     *
     * @throws Throwable
     */
    public function update(ChronicDiseasesUpdateRequest $request, ChronicDiseases $chronicDisease): ChronicDiseasesResource
    {
        $updatedChronicDiseases = $this->chronicDiseasesService->update(ChronicDiseasesData::from($request->all()), $chronicDisease);

        return ChronicDiseasesResource::make($updatedChronicDiseases)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a chronicDiseases.
     */
    public function destroy(ChronicDiseases $chronicDisease): ChronicDiseasesResource
    {
        $chronicDisease->delete();

        return ChronicDiseasesResource::make($chronicDisease)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
