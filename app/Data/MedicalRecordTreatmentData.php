<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ToothPositionEnum;
use App\Models\MedicalRecordTreatment;
use Carbon\Carbon;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class MedicalRecordTreatmentData extends Data
{
    use HasModelAttributes;

    /** @var class-string<MedicalRecordTreatment> */
    protected static string $model = MedicalRecordTreatment::class;

    public function __construct(
        public ?string $medicalRecordId,
        #[Enum(ToothPositionEnum::class)]
        public ?ToothPositionEnum $toothPosition,
        #[Date, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'Y-m-d\TH:i:sP'])]
        public ?Carbon $treatmentDate,
        public ?string $treatmentCost,
        public ?string $treatmentDescription,
        public ?string $fillerMaterialId,
        public ?string $treatmentId,
        public ?string $dentalLabId,
        public ?int $sessionNumber,
        /** @var array<string> */
        public ?array $doctorIds,
    ) {}
}
