<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\PatientGenderEnum;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class PatientData extends Data
{
    use HasModelAttributes;

    /** @var class-string<Patient> */
    protected static string $model = Patient::class;

    public function __construct(
        #[Max(255)]
        public ?string $name,
        #[Max(255), Unique('patients', 'email')]
        public ?string $email,
        #[Max(255), Unique('patients', 'phone_number')]
        public ?string $phoneNumber,
        #[Date, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'Y-m-d\TH:i:sP'])]
        public ?Carbon $birthday,
        #[Enum(PatientGenderEnum::class)]
        public ?PatientGenderEnum $gender,
        #[Max(255)]
        public ?string $city,
        #[Max(255)]
        public ?string $streetAddress,
        #[Date, WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d', 'Y-m-d\TH:i:sP'])]
        public ?Carbon $registrationDate,
        #[Max(255)]
        public ?string $notes,
        #[File]
        public ?UploadedFile $primaryImage
    ) {}
}
