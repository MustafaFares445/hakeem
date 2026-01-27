<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\MedicalRecordTreatment;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait MedicalRecordTreatmentFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(MedicalRecordTreatment::class)
            ->allowedFilters([
                AllowedFilter::exact('medicalRecordId', 'medical_record_id'),
                AllowedFilter::exact('treatmentId', 'treatment_id'),
                AllowedFilter::exact('toothPosition', 'tooth_position'),
                AllowedFilter::exact('fillerMaterialId', 'filler_material_id'),
                AllowedFilter::exact('dentalLabId', 'dental_lab_id'),
                AllowedFilter::scope('treatmentDateAfter'),
                AllowedFilter::scope('treatmentDateBefore'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('treatmentDate', 'treatment_date'),
                AllowedSort::field('treatmentCost', 'treatment_cost'),
                AllowedSort::field('sessionNumber', 'session_number'),
            ])
            ->defaultSort('-created_at');
    }

    public function scopeTreatmentDateAfter($query, $date)
    {
        $dateTime = is_string($date) ? \Carbon\Carbon::parse($date)->startOfDay() : $date;

        return $query->where('treatment_date', '>=', $dateTime);
    }

    public function scopeTreatmentDateBefore($query, $date)
    {
        $dateTime = is_string($date) ? \Carbon\Carbon::parse($date)->endOfDay() : $date;

        return $query->where('treatment_date', '<=', $dateTime);
    }

    public function scopeCreatedAfter($query, $date)
    {
        $dateTime = is_string($date) ? \Carbon\Carbon::parse($date)->startOfDay() : $date;

        return $query->where('created_at', '>=', $dateTime);
    }

    public function scopeCreatedBefore($query, $date)
    {
        $dateTime = is_string($date) ? \Carbon\Carbon::parse($date)->endOfDay() : $date;

        return $query->where('created_at', '<=', $dateTime);
    }

    public function scopeSearch($query, $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $likeTerm = SearchTermEscaper::escape($search);

        return $query->where(function (Builder $q) use ($likeTerm) {
            $q->whereRaw("treatment_description LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("tooth_position LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
