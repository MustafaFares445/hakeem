<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\MedicalRecordTreatmentData;
use App\Http\Requests\MedicalRecordTreatmentRequests\MedicalRecordTreatmentFilterRequest;
use App\Http\Requests\MedicalRecordTreatmentRequests\MedicalRecordTreatmentStoreRequest;
use App\Http\Requests\MedicalRecordTreatmentRequests\MedicalRecordTreatmentUpdateRequest;
use App\Http\Resources\MedicalRecordTreatmentResource;
use App\Models\MedicalRecordTreatment;
use App\Services\MedicalRecordTreatmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class MedicalRecordTreatmentController
{
    use AuthorizesRequests;

    public function __construct(private MedicalRecordTreatmentService $medicalRecordTreatmentService) {}

    /**
     * Get a paginated list of medical record treatments with optional filtering.
     *
     * @return AnonymousResourceCollection<MedicalRecordTreatmentResource>
     */
    public function index(MedicalRecordTreatmentFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', MedicalRecordTreatment::class);

        $treatments = MedicalRecordTreatment::getQuery()
            ->with(['treatment', 'dentalLab', 'doctors'])
            ->paginate($request->input('perPage', 20));

        return MedicalRecordTreatmentResource::collection($treatments)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new medical record treatment.
     *
     * @throws Throwable
     */
    public function store(MedicalRecordTreatmentStoreRequest $request): JsonResponse
    {
        $this->authorize('create', MedicalRecordTreatment::class);

        $treatment = $this->medicalRecordTreatmentService->store(MedicalRecordTreatmentData::from($request->validated()));

        return MedicalRecordTreatmentResource::make($treatment->load(['treatment', 'dentalLab', 'doctors']))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific medical record treatment by ID.
     */
    public function show(MedicalRecordTreatment $medicalRecordTreatment): MedicalRecordTreatmentResource
    {
        $this->authorize('view', $medicalRecordTreatment);

        return MedicalRecordTreatmentResource::make($medicalRecordTreatment->load(['treatment', 'dentalLab', 'doctors']))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing medical record treatment.
     *
     * @throws Throwable
     */
    public function update(MedicalRecordTreatmentUpdateRequest $request, MedicalRecordTreatment $medicalRecordTreatment): MedicalRecordTreatmentResource
    {
        $this->authorize('update', $medicalRecordTreatment);

        $updated = $this->medicalRecordTreatmentService->update(MedicalRecordTreatmentData::from($request->validated()), $medicalRecordTreatment);

        return MedicalRecordTreatmentResource::make($updated->load(['treatment', 'dentalLab', 'doctors']))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a medical record treatment.
     */
    public function destroy(MedicalRecordTreatment $medicalRecordTreatment): MedicalRecordTreatmentResource
    {
        $this->authorize('delete', $medicalRecordTreatment);

        $medicalRecordTreatment->delete();

        return MedicalRecordTreatmentResource::make($medicalRecordTreatment)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
