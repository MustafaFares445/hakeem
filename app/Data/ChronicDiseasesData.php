<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\ChronicDiseases;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Data;

final class ChronicDiseasesData extends Data
{
    use HasModelAttributes;

    /** @var class-string<ChronicDiseases> */
    protected static string $model = ChronicDiseases::class;

    public function __construct(
        public ?string $patientId = null,
        public ?string $title = null,
    ) {}
}
