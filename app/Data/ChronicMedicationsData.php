<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\ChronicMedications;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class ChronicMedicationsData extends Data
{
    use HasModelAttributes;

    /** @var class-string<ChronicMedications> */
    protected static string $model = ChronicMedications::class;

    public function __construct(
        #[Max(36)]
        public ?string $patientId,
        public ?string $title
    ) {}
}
