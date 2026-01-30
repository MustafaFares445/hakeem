<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class MediaStoreData extends Data
{
    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function __construct(
        #[Max(36)]
        public string $patientId,
        #[Max(36)]
        public ?string $medicalRecordId,
        /** @var array<int, UploadedFile> */
        public array $files,
        #[Max(255)]
        public ?string $collection,
    ) {}

    public function collectionName(): string
    {
        return $this->collection ?? 'other';
    }
}
