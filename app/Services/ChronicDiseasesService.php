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
            return ChronicDiseases::create($data->onlyModelAttributes());
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

            tap($chronicDiseases)->update($data->onlyModelAttributes());

            return $chronicDiseases;
        });
    }
}
