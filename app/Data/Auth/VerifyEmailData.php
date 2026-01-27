<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

final class VerifyEmailData extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        #[Min(6), Max(6)]
        public string $otp,
    ) {}
}
