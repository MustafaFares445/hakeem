<?php

declare(strict_types=1);

namespace App\Enums;

enum RecordTypeEnum: string
{
    case InClinic = 'in_clinic';
    case External = 'external';

    public function label(): string
    {
        return match ($this) {
            self::InClinic => 'In Clinic',
            self::External => 'External',
        };
    }
}
