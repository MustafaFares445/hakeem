<?php

declare(strict_types=1);

namespace App\Data\Auth;

use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

final class RegisterData extends Data
{
    use HasModelAttributes;

    /** @var class-string<User> */
    protected static string $model = User::class;

    public function __construct(
        #[Max(191)]
        public string $name,
        #[Min(8), Max(191)]
        public string $password,
        #[Max(36)]
        public string $phoneNumber,
    ) {}
}
