<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionGroup: string
{
    case CHRONIC_DISEASES = 'chronic_diseases';
    case PATIENT = 'patients';
    case USER = 'users';
}
