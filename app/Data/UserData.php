<?php

declare(strict_types=1);

namespace App\Data;

use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Date;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Exists;


class UserData extends Data
{
    use HasModelAttributes;

/** @var class-string<User> */
    protected static string $model = User::class;

    public function __construct(
        #[Max(255)]
        public ?string $name,
        #[Max(255), Unique('users', 'email')]
        public ?string $email,
        #[Date]
        public ?Carbon $emailVerifiedAt,
        #[File]
        public ?UploadedFile $primaryImage,
        #[File]
        public ?array $images,
        #[Exists('media', 'id')]
        public ?int $modelId,
        public ?string $modelType
    ) {}
}
