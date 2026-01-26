<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait PatientFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(Patient::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::partial('phoneNumber', 'phone_number'),
                AllowedFilter::partial('birthday'),
                AllowedFilter::exact('gender'),
                AllowedFilter::partial('city'),
                AllowedFilter::partial('streetAddress', 'street_address'),
                AllowedFilter::partial('registrationDate', 'registration_date'),
                AllowedFilter::partial('notes'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
                AllowedSort::field('email'),
                AllowedSort::field('phoneNumber', 'phone_number'),
                AllowedSort::field('birthday'),
                AllowedSort::field('gender'),
                AllowedSort::field('city'),
                AllowedSort::field('streetAddress', 'street_address'),
                AllowedSort::field('registrationDate', 'registration_date'),
                AllowedSort::field('notes'),
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
                ->orWhereRaw("email LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("phone_number LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("gender LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("city LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("street_address LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("notes LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
