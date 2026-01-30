<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\MediaStoreData;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mrmarchone\LaravelAutoCrud\Helpers\MediaHelper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

final class MediaService
{
    /**
     * @return Collection<int, Media>
     *
     * @throws Throwable
     */
    public function store(MediaStoreData $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $model = $this->resolveTargetModel($data);

            $result = MediaHelper::uploadMedia(
                $data->files,
                $model,
                $data->collectionName()
            );

            return collect($result);
        });
    }

    private function resolveTargetModel(MediaStoreData $data): Patient|MedicalRecord
    {
        $patient = Patient::findOrFail($data->patientId);

        if ($data->medicalRecordId === null) {
            return $patient;
        }

        return MedicalRecord::query()
            ->where('id', $data->medicalRecordId)
            ->where('patient_id', $patient->id)
            ->firstOrFail();
    }
}
