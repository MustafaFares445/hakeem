<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\FillerMaterial;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Data;

final class FillerMaterialData extends Data
{
    use HasModelAttributes;

    /** @var class-string<FillerMaterial> */
    protected static string $model = FillerMaterial::class;

    public function __construct(
        public string $name,
        public ?string $description,
        public ?bool $isActive = true,
    ) {}
}
