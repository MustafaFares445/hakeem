<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\MedicalRecordTreatmentData;
use App\Models\MedicalRecordTreatment;
use Illuminate\Support\Facades\DB;
use Throwable;

final class MedicalRecordTreatmentService
{
    /**
     * Store medical record treatment with doctor assignments.
     *
     * @throws Throwable
     */
    public function store(MedicalRecordTreatmentData $data): MedicalRecordTreatment
    {
        return DB::transaction(static function () use ($data) {
            $treatment = MedicalRecordTreatment::create($data->onlyModelAttributes());

            if ($data->doctorIds) {
                $treatment->doctors()->sync($data->doctorIds);
            }

            return $treatment;
        });
    }

    /**
     * Update medical record treatment with doctor assignments.
     *
     * @throws Throwable
     */
    public function update(MedicalRecordTreatmentData $data, MedicalRecordTreatment $treatment): MedicalRecordTreatment
    {
        return DB::transaction(static function () use ($data, $treatment) {
            tap($treatment)->update($data->onlyModelAttributes());

            if ($data->doctorIds) {
                $treatment->doctors()->sync($data->doctorIds);
            }

            return $treatment;
        });
    }
}
