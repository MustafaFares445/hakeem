<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait MedicalRecordFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(MedicalRecord::class)
            ->allowedFilters([
                AllowedFilter::exact('patientId', 'patient_id'),
                AllowedFilter::exact('recordType', 'record_type'),
                AllowedFilter::partial('caseName', 'case_name'),
                AllowedFilter::partial('description'),
                AllowedFilter::scope('recordDateAfter'),
                AllowedFilter::scope('recordDateBefore'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('caseName', 'case_name'),
                AllowedSort::field('recordDate', 'record_date'),
                AllowedSort::field('totalCost', 'total_cost'),
            ])
            ->defaultSort('-created_at');
    }

    public function scopeRecordDateAfter($query, $date)
    {
        $dateTime = is_string($date) ? \Carbon\Carbon::parse($date)->startOfDay() : $date;

        return $query->where('record_date', '>=', $dateTime);
    }

    public function scopeRecordDateBefore($query, $date)
    {
        $dateTime = is_string($date) ? \Carbon\Carbon::parse($date)->endOfDay() : $date;

        return $query->where('record_date', '<=', $dateTime);
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
            $q->whereRaw("case_name LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("description LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
