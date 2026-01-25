<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionGroup: string
{
    case PATIENT = 'patients';
    case USER = 'users';
}
