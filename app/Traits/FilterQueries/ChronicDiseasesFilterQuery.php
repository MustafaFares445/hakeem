<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\ChronicDiseases;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait ChronicDiseasesFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(ChronicDiseases::class)
            ->allowedFilters([
                AllowedFilter::partial('patientId', 'patient_id'),
                AllowedFilter::partial('title'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('patientId', 'patient_id'),
                AllowedSort::field('title'),
            ])
            ->defaultSort('-created_at');
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
            $q->whereRaw("patient_id LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("title LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
