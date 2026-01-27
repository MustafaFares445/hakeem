<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    use HasModelAttributes;

    /** @var class-string<User> */
    protected static string $model = User::class;

    public function __construct(
        #[Max(255)]
        public ?string $name,
        #[Max(191), Unique('users', 'username')]
        public ?string $username,
        #[Max(255), Unique('users', 'email')]
        public ?string $email,
        /** @var array<string>|null */
        public ?array $roles = null,
        #[File]
        public ?UploadedFile $primaryImage = null,
        #[Max(255)]
        public ?string $password
    ) {}
}
