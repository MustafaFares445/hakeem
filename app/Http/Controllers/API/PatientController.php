<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\PatientData;
use App\Http\Requests\PatientRequests\PatientFilterRequest;
use App\Http\Requests\PatientRequests\PatientStoreRequest;
use App\Http\Requests\PatientRequests\PatientUpdateRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class PatientController
{
    public function __construct(private PatientService $patientService) {}

    /**
     * Get a paginated list of patients with optional filtering.
     *
     * @return PatientResource
     */
    public function index(PatientFilterRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->get('perPage') ?? $request->get('per_page', 20);
        $patients = Patient::getQuery()
            ->paginate($perPage);

        return PatientResource::collection($patients)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new patient.
     *
     * @throws Throwable
     */
    public function store(PatientStoreRequest $request): JsonResponse
    {
        $patient = $this->patientService->store(PatientData::from($request->validated()));

        return PatientResource::make($patient->load('media'))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific patient by ID.
     */
    public function show(Patient $patient): PatientResource
    {
        return PatientResource::make($patient->load('media'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing patient.
     *
     * @throws Throwable
     */
    public function update(PatientUpdateRequest $request, Patient $patient): PatientResource
    {
        $updatedPatient = $this->patientService->update(PatientData::from($request->validated()), $patient);

        return PatientResource::make($updatedPatient->load('media'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a patient.
     */
    public function destroy(Patient $patient): PatientResource
    {
        $patient->delete();

        return PatientResource::make($patient)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
