<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionGroup: string
{
    case BOOKING = 'bookings';
    case CHRONIC_DISEASES = 'chronic_diseases';
    case CHRONIC_MEDICATIONS = 'chronic_medications';
    case PATIENT = 'patients';
    case USER = 'users';
}
