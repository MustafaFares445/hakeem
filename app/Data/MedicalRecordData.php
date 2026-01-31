<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\RecordTypeEnum;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class MedicalRecordData extends Data
{
    use HasModelAttributes;

    /** @var class-string<MedicalRecord> */
    protected static string $model = MedicalRecord::class;

    public function __construct(
        #[Max(36)]
        public ?string $patientId,
        #[Date, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'Y-m-d\TH:i:sP'])]
        public ?Carbon $recordDate,
        #[Enum(RecordTypeEnum::class)]
        public ?RecordTypeEnum $recordType,
        #[Max(255)]
        public ?string $caseName,
        #[Max(1000)]
        public ?string $description,
    ) {}
}
