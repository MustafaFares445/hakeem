<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: string
{
    case SystemAdmin = 'system admin';
    case Doctor = 'doctor';
    case Secretariat = 'Secretariat';
}
