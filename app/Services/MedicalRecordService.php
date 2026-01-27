<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\MedicalRecordData;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\DB;
use Throwable;

final class MedicalRecordService
{
    /**
     * Store medical record data with attachments.
     *
     * @throws Throwable
     */
    public function store(MedicalRecordData $data): MedicalRecord
    {
        return DB::transaction(static function () use ($data) {
            return MedicalRecord::create($data->onlyModelAttributes());
        });
    }

    /**
     * Update medical record data.
     *
     * @throws Throwable
     */
    public function update(MedicalRecordData $data, MedicalRecord $medicalRecord): MedicalRecord
    {
        return DB::transaction(static function () use ($data, $medicalRecord) {
            return tap($medicalRecord)->update($data->onlyModelAttributes());
        });
    }
}
