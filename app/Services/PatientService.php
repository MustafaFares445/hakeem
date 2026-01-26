<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\PatientData;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Mrmarchone\LaravelAutoCrud\Helpers\MediaHelper;
use Throwable;

final class PatientService
{
    /**
     * Validate patient data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(PatientData $data): Patient
    {
        return DB::transaction(static function () use ($data) {
            $patient = Patient::create($data->onlyModelAttributes());

            MediaHelper::uploadMedia($data->primaryImage, $patient, 'primary-image');

            return $patient;
        });
    }

    /**
     * Update patient data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(PatientData $data, Patient $patient): Patient
    {
        return DB::transaction(static function () use ($data, $patient) {
            tap($patient)->update($data->onlyModelAttributes());

            MediaHelper::updateMedia($data->primaryImage, $patient, 'primary-image');

            return $patient;
        });
    }
}
