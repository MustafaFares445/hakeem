<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\PatientData;
use App\Http\Requests\PatientRequests\PatientFilterRequest;
use App\Http\Requests\PatientRequests\PatientStoreRequest;
use App\Http\Requests\PatientRequests\PatientUpdateRequest;
use App\Http\Resources\PatientResource;
use App\Http\Resources\ToothOverviewResource;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class PatientController
{
    use AuthorizesRequests;

    public function __construct(private PatientService $patientService) {}

    /**
     * Get a paginated list of patients with optional filtering.
     *
     * @return AnonymousResourceCollection<PatientResource>
     */
    public function index(PatientFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::getQuery()
            ->paginate($request->input('perPage', 20));

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
        $this->authorize('create', Patient::class);

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
        $this->authorize('view', $patient);

        return PatientResource::make($patient->load(['media', 'lastAppointment', 'firstAppointment']))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing patient.
     *
     * @throws Throwable
     */
    public function update(PatientUpdateRequest $request, Patient $patient): PatientResource
    {
        $this->authorize('update', $patient);

        $updatedPatient = $this->patientService->update(PatientData::from($request->validated()), $patient);

        return PatientResource::make($updatedPatient->load('media'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a patient.
     */
    public function destroy(Patient $patient): PatientResource
    {
        $this->authorize('delete', $patient);

        $patient->delete();

        return PatientResource::make($patient)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }

    /**
     * Get tooth overview for a patient with treatment history.
     */
    public function toothOverview(Patient $patient): ToothOverviewResource
    {
        $this->authorize('view', $patient);

        return ToothOverviewResource::make($patient->load('medicalRecords'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }
}
