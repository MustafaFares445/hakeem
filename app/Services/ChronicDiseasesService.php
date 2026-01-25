<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ChronicDiseasesData;
use App\Models\ChronicDiseases;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ChronicDiseasesService
{
    /**
     * Validate chronicDiseases data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(ChronicDiseasesData $data): ChronicDiseases
    {
        return DB::transaction(static function () use ($data) {
            $attributes = [];
            if (isset($data->patientId) && $data->patientId !== null) {
                $attributes['patient_id'] = $data->patientId;
            }
            if (isset($data->title) && $data->title !== null) {
                $attributes['title'] = $data->title;
            }
            $chronicDiseases = ChronicDiseases::create($attributes);

            return $chronicDiseases;
        });
    }

    /**
     * Update chronicDiseases data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(ChronicDiseasesData $data, ChronicDiseases $chronicDiseases): ChronicDiseases
    {
        return DB::transaction(static function () use ($data, $chronicDiseases) {
            $attributes = [];
            if ($data->patientId !== null) {
                $attributes['patient_id'] = $data->patientId;
            }
            if ($data->title !== null) {
                $attributes['title'] = $data->title;
            }
            tap($chronicDiseases)->update($attributes);

            return $chronicDiseases;
        });
    }
}
