<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** @mixin Media */
final class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $url = $this->disk ? $this->getFullUrl() : null;
        $thumbnailUrl = $this->disk
            ? ($this->hasGeneratedConversion('thumb') ? $this->getFullUrl('thumb') : $this->getFullUrl())
            : null;

        $size = $this->size !== null ? $this->human_readable_size : null;

        $id = $this->resource->getKey();
        if ($id === null && request()->route('media')) {
            $param = request()->route('media');
            $id = $param instanceof \Illuminate\Database\Eloquent\Model
                ? $param->getKey()
                : (is_numeric($param) ? (int) $param : $param);
        }
        if ($id === null) {
            $lastSegment = request()->segment(count(request()->segments()));
            $id = is_numeric($lastSegment) ? (int) $lastSegment : $lastSegment;
        }

        return [
            /** @example "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f" */
            'id' => $id,
            /** @example "document" */
            'name' => $this->name,
            /** @example "document.pdf" */
            'fileName' => $this->file_name,
            /** @example "attachments" */
            'collection' => $this->collection_name,
            /** @example "https://example.com/storage/1/document.pdf" */
            'url' => $url,
            /** @example "https://example.com/storage/1/document_thumb.pdf" */
            'thumbnailUrl' => $thumbnailUrl,
            /** @example "1 KB" */
            'size' => $size,
            /** @example "pdf" */
            'extension' => $this->extension,
            /** @example "application/pdf" */
            'type' => $this->extension ? $this->getTypeFromExtension() : null,
            /** @example "document" */
            'caption' => $this->getCustomProperty('caption') ?? $this->name,
            /** @example "2025-01-31 10:00:00" */
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
