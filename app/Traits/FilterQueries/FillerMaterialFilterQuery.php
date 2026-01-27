<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\FillerMaterial;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait FillerMaterialFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(FillerMaterial::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('description'),
                AllowedFilter::exact('isActive', 'is_active'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
                AllowedSort::field('createdAt', 'created_at'),
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

        return $query->where(function (Builder $q) use ($likeTerm): void {
            $q->whereRaw("name LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("description LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
