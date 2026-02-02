<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\FillerMaterialColorEnum;
use App\Models\FillerMaterial;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Data;

final class FillerMaterialData extends Data
{
    use HasModelAttributes;

    /** @var class-string<FillerMaterial> */
    protected static string $model = FillerMaterial::class;

    public function __construct(
        public string $name,
        #[Enum(FillerMaterialColorEnum::class)]
        public ?FillerMaterialColorEnum $color = null,
        #[Uuid, Exists('dental_labs', 'id')]
        public ?string $dentalLabId = null,
        public ?string $description = null,
        public ?bool $isActive = true,
    ) {}
}
