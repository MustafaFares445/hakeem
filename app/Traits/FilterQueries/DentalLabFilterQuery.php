<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\DentalLab;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait DentalLabFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(DentalLab::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('phone'),
                AllowedFilter::partial('address'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
                AllowedSort::field('phone'),
                AllowedSort::field('address'),
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
            $q->whereRaw("name LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("phone LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("address LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
