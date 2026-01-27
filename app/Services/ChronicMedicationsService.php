<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ChronicMedicationsData;
use App\Models\ChronicMedications;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ChronicMedicationsService
{
    /**
     * Validate chronicMedications data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(ChronicMedicationsData $data): ChronicMedications
    {
        return DB::transaction(static function () use ($data) {
            return ChronicMedications::create($data->onlyModelAttributes());
        });
    }

    /**
     * Update chronicMedications data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(ChronicMedicationsData $data, ChronicMedications $chronicMedications): ChronicMedications
    {
        return DB::transaction(static function () use ($data, $chronicMedications) {
            tap($chronicMedications)->update($data->onlyModelAttributes());

            return $chronicMedications;
        });
    }
}
