<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Mrmarchone\LaravelAutoCrud\Helpers\SearchTermEscaper;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait BookingFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(Booking::class)
            ->allowedFilters([
                AllowedFilter::callback('patientId', function (Builder $query, mixed $value) {
                    // Treat explicit "null" (string) or empty / null as a request
                    // to filter bookings where patient_id IS NULL.
                    if ($value === null || $value === '' || (is_string($value) && strtolower(trim($value)) === 'null')) {
                        return $query->whereNull('patient_id');
                    }

                    return $query->where('patient_id', $value);
                }),
                AllowedFilter::callback('tenantId', function (Builder $query, mixed $value) {
                    if ($value === null || $value === '' || (is_string($value) && strtolower(trim($value)) === 'null')) {
                        return $query->whereNull('tenant_id');
                    }

                    return $query->where('tenant_id', $value);
                }),
                AllowedFilter::callback('userId', function (Builder $query, mixed $value) {
                    if ($value === null || $value === '' || (is_string($value) && strtolower(trim($value)) === 'null')) {
                        return $query->whereNull('user_id');
                    }

                    return $query->where('user_id', $value);
                }),
                AllowedFilter::partial('date'),
                AllowedFilter::partial('time'),
                AllowedFilter::partial('appointmentType', 'appointment_type'),
                AllowedFilter::scope('createdAfter'),
                AllowedFilter::scope('createdBefore'),
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                AllowedSort::field('patientId', 'patient_id'),
                AllowedSort::field('tenantId', 'tenant_id'),
                AllowedSort::field('userId', 'user_id'),
                AllowedSort::field('date'),
                AllowedSort::field('time'),
                AllowedSort::field('appointmentType', 'appointment_type'),
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

    public function scopePatientId($query, $value)
    {
        if ($value === '' || $value === null || (is_string($value) && trim($value) === '')) {
            return $query->whereNull('patient_id');
        }
        return $query->where('patient_id', $value);
    }

    public function scopeTenantId($query, $value)
    {
        if ($value === '' || $value === null || (is_string($value) && trim($value) === '')) {
            return $query->whereNull('tenant_id');
        }
        return $query->where('tenant_id', $value);
    }

    public function scopeUserId($query, $value)
    {
        if ($value === '' || $value === null || (is_string($value) && trim($value) === '')) {
            return $query->whereNull('user_id');
        }
        return $query->where('user_id', $value);
    }

    public function scopeSearch($query, $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $likeTerm = SearchTermEscaper::escape($search);

        return $query->where(function (Builder $q) use ($likeTerm) {
            $q->whereRaw("patient_id LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("tenant_id LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("user_id LIKE ? ESCAPE '!'", [$likeTerm])
                ->orWhereRaw("appointment_type LIKE ? ESCAPE '!'", [$likeTerm]);
        });
    }
}
