<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class LoginData extends Data
{
    public function __construct(
        #[Max(191)]
        public string $username,
        #[Max(191)]
        public string $password,
    ) {}
}
