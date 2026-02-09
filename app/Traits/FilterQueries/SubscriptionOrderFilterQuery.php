<?php

declare(strict_types=1);

namespace App\Traits\FilterQueries;

use App\Models\SubscriptionOrder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

trait SubscriptionOrderFilterQuery
{
    public static function getQuery(): QueryBuilder
    {
        return QueryBuilder::for(SubscriptionOrder::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts([
                AllowedSort::field('createdAt', 'created_at'),
                AllowedSort::field('status'),
            ])
            ->defaultSort('-created_at');
    }
}
