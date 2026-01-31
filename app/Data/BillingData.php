<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use App\Models\Billing;
use Carbon\Carbon;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class BillingData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Billing> */
    protected static string $model = Billing::class;

    public function __construct(
        #[Max(36)]
        public ?string $tenantId,
        #[Enum(BillingTypeEnum::class)]
        public ?BillingTypeEnum $type,
        #[Date, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'Y-m-d\TH:i:sP'])]
        public ?Carbon $date,
        #[Max(36)]
        public ?string $patientId,
        #[Max(36)]
        public ?string $userId,
        #[Max(36)]
        public ?string $medicalRecordId,
        #[Max(255)]
        public ?string $caseName,
        public ?float $paidAmount,
        public ?float $totalCost,
        #[Max(255)]
        public ?string $itemName,
        public ?int $quantity,
        public ?float $amount,
        #[Enum(BillingOutgoingTypeEnum::class)]
        public ?BillingOutgoingTypeEnum $outgoingType
    ) {}
}
