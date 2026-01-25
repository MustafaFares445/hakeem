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

final class ChronicDiseasesController
{
    public function __construct(private ChronicDiseasesService $chronicDiseasesService) {}

    /**
     * Get a paginated list of chronic_diseases with optional filtering.
     *
     * @return ChronicDiseasesResource
     */
    public function index(ChronicDiseasesFilterRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->get('perPage') ?? $request->get('per_page', 20);
        $chronicDiseasess = ChronicDiseases::getQuery()
            ->paginate($perPage);

        return ChronicDiseasesResource::collection($chronicDiseasess)
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
     *
     * @param  ChronicDiseases  $chronicDiseases
     */
    public function show(ChronicDiseases $chronicDisease): ChronicDiseasesResource
    {
        return ChronicDiseasesResource::make($chronicDisease)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing chronicDiseases.
     *
     * @param  ChronicDiseases  $chronicDiseases
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
     *
     * @param  ChronicDiseases  $chronicDiseases
     */
    public function destroy(ChronicDiseases $chronicDisease): ChronicDiseasesResource
    {
        $id = $chronicDisease->id;
        $chronicDisease->delete();

        $chronicDisease->id = $id;

        return ChronicDiseasesResource::make($chronicDisease)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
