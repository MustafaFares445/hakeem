<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionDurationUnitEnum: string
{
    case Day = 'day';
    case Month = 'month';
    case Year = 'year';
}
