<?php

declare(strict_types=1);

namespace App\Enums;

enum BillingTypeEnum: string
{
    case Incoming = 'incoming';
    case Outgoing = 'outgoing';
}
