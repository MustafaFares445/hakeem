<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentTypeEnum: string
{
    case Preview = 'preview';
    case Surgery = 'surgery';
    case Review = 'review';
}
