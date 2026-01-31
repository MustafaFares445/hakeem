<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\MedicalRecordData;
use App\Http\Requests\MedicalRecordRequests\MedicalRecordFilterRequest;
use App\Http\Requests\MedicalRecordRequests\MedicalRecordStoreRequest;
use App\Http\Requests\MedicalRecordRequests\MedicalRecordUpdateRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Services\MedicalRecordService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class MedicalRecordController
{
    use AuthorizesRequests;

    public function __construct(private MedicalRecordService $medicalRecordService) {}

    /**
     * Get a paginated list of medical records with optional filtering.
     *
     * @return AnonymousResourceCollection<MedicalRecordResource>
     */
    public function index(MedicalRecordFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', MedicalRecord::class);

        $medicalRecords = MedicalRecord::getQuery()
            ->with(['patient', 'treatments', 'billings'])
            ->paginate($request->input('perPage', 20));

        return MedicalRecordResource::collection($medicalRecords)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new medical record.
     *
     * @throws Throwable
     */
    public function store(MedicalRecordStoreRequest $request): JsonResponse
    {
        $this->authorize('create', MedicalRecord::class);

        $validated = $request->validated();
        $medicalRecord = $this->medicalRecordService->store(
            MedicalRecordData::from($validated),
            isset($validated['totalCost']) ? (float) $validated['totalCost'] : null,
            isset($validated['paidAmount']) ? (float) $validated['paidAmount'] : null
        );

        return MedicalRecordResource::make($medicalRecord->load(['patient', 'treatments', 'media', 'billings']))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific medical record by ID.
     */
    public function show(MedicalRecord $medicalRecord): MedicalRecordResource
    {
        $this->authorize('view', $medicalRecord);

        return MedicalRecordResource::make($medicalRecord->load(['patient', 'treatments', 'media', 'billings']))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing medical record.
     *
     * @throws Throwable
     */
    public function update(MedicalRecordUpdateRequest $request, MedicalRecord $medicalRecord): MedicalRecordResource
    {
        $this->authorize('update', $medicalRecord);

        $validated = $request->validated();
        $updatedMedicalRecord = $this->medicalRecordService->update(
            MedicalRecordData::from($validated),
            $medicalRecord,
            isset($validated['totalCost']) ? (float) $validated['totalCost'] : null,
            isset($validated['paidAmount']) ? (float) $validated['paidAmount'] : null
        );

        return MedicalRecordResource::make($updatedMedicalRecord->load(['patient', 'treatments', 'media', 'billings']))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a medical record.
     */
    public function destroy(MedicalRecord $medicalRecord): MedicalRecordResource
    {
        $this->authorize('delete', $medicalRecord);

        $medicalRecord->delete();

        return MedicalRecordResource::make($medicalRecord)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
