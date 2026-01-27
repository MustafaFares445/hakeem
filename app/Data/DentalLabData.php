<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\DentalLab;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class DentalLabData extends Data
{
    use HasModelAttributes;

    /** @var class-string<DentalLab> */
    protected static string $model = DentalLab::class;

    public function __construct(
        #[Max(255)]
        public ?string $name,
        #[Max(20)]
        public ?string $phone,
        #[Max(255)]
        public ?string $address,
    ) {}
}
