<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Booking;
use Carbon\Carbon;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class BookingData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Booking> */
    protected static string $model = Booking::class;

    public function __construct(
        #[Max(36)]
        public ?string $patientId,
        #[Max(36)]
        public ?string $tenantId,
        #[Max(36)]
        public ?string $userId,
        #[Date, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'Y-m-d\TH:i:sP'])]
        public ?Carbon $date,
        public ?string $time,
        #[Max(255)]
        public ?string $appointmentType
    ) {}
}
