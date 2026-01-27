<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Treatment;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class TreatmentData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Treatment> */
    protected static string $model = Treatment::class;

    public function __construct(
        #[Max(255)]
        public ?string $name,
        #[Max(1000)]
        public ?string $description,
        public ?string $defaultCost,
    ) {}
}
