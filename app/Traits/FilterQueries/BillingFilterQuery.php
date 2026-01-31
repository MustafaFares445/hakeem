<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\Billing;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait BillingFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(Billing::class)
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::callback('patientId', function (Builder $query, mixed $value) {
                    if ($value === null || $value === '' || (is_string($value) && mb_strtolower(mb_trim($value)) === 'null')) {
                        return $query->whereNull('patient_id');
                    }

                    return $query->where('patient_id', $value);
                }),
                AllowedFilter::callback('userId', function (Builder $query, mixed $value) {
                    if ($value === null || $value === '' || (is_string($value) && mb_strtolower(mb_trim($value)) === 'null')) {
                        return $query->whereNull('user_id');
                    }

                    return $query->where('user_id', $value);
                }),
                AllowedFilter::callback('medicalRecordId', function (Builder $query, mixed $value) {
                    if ($value === null || $value === '' || (is_string($value) && mb_strtolower(mb_trim($value)) === 'null')) {
                        return $query->whereNull('medical_record_id');
                    }

                    return $query->where('medical_record_id', $value);
                }),
                AllowedFilter::callback('tenantId', function (Builder $query, mixed $value) {
                    if ($value === null || $value === '' || (is_string($value) && mb_strtolower(mb_trim($value)) === 'null')) {
                        return $query->whereNull('tenant_id');
                    }

                    return $query->where('tenant_id', $value);
                }),
                AllowedFilter::partial('date'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('type'),
                AllowedSort::field('patientId', 'patient_id'),
                AllowedSort::field('userId', 'user_id'),
                AllowedSort::field('medicalRecordId', 'medical_record_id'),
                AllowedSort::field('tenantId', 'tenant_id'),
                AllowedSort::field('date'),
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

        return $query->where(function (Builder $q) use ($likeTerm) {
            $q->whereRaw('case_name LIKE ? ESCAPE \'!\'', [$likeTerm])
                ->orWhereRaw('item_name LIKE ? ESCAPE \'!\'', [$likeTerm])
                ->orWhereRaw('type LIKE ? ESCAPE \'!\'', [$likeTerm])
                ->orWhereRaw('outgoing_type LIKE ? ESCAPE \'!\'', [$likeTerm]);
        });
    }
}
