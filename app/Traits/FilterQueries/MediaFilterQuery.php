<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\Media;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait MediaFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(Media::class)
            ->allowedFilters([
                AllowedFilter::scope('patientAndMedicalRecords', 'forPatientAndMedicalRecords'),
                AllowedFilter::exact('collectionName', 'collection_name'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
                AllowedSort::field('fileName', 'file_name'),
                AllowedSort::field('size'),
                AllowedSort::field('createdAt', 'created_at'),
            ])
            ->defaultSort('-created_at');
    }

    public function scopeForPatientAndMedicalRecords(Builder $query, string $patientId): Builder
    {
        $medicalRecordSubquery = MedicalRecord::query()
            ->select('id')
            ->where('patient_id', $patientId);

        return $query->where(function (Builder $q) use ($patientId, $medicalRecordSubquery): void {
            $q->where('model_type', Patient::class)
                ->where('model_id', $patientId)
                ->orWhere(function (Builder $q) use ($medicalRecordSubquery): void {
                    $q->where('model_type', MedicalRecord::class)
                        ->whereIn('model_id', $medicalRecordSubquery);
                });
        });
    }

    public function scopeCreatedAfter(Builder $query, string $date): Builder
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeCreatedBefore(Builder $query, string $date): Builder
    {
        return $query->where('created_at', '<=', $date);
    }
}
