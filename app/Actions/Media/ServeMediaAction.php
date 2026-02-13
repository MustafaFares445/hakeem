<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final readonly class ServeMediaAction
{
    public function __construct(private FilesystemManager $filesystem) {}

    public function download(Media $media): StreamedResponse
    {
        return $this->buildResponse($media, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    public function stream(Media $media): StreamedResponse
    {
        return $this->buildResponse($media, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    private function buildResponse(Media $media, string $disposition): StreamedResponse
    {
        $disk = $this->filesystem->disk($media->disk);
        $path = $media->getPathRelativeToRoot();
        $stream = $this->openStream($disk, $path);

        $headers = [
            'Content-Type' => $media->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => HeaderUtils::makeDisposition($disposition, $media->file_name),
        ];

        $size = $this->resolveFileSize($disk, $path);
        if ($size !== null) {
            $headers['Content-Length'] = (string) $size;
        }

        return response()->stream(function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, Response::HTTP_OK, $headers);
    }

    /**
     * @return resource
     */
    private function openStream(FilesystemAdapter $disk, string $path)
    {
        if (! $disk->exists($path)) {
            throw new NotFoundHttpException('Media file not found.');
        }

        $stream = $disk->readStream($path);
        if ($stream === false) {
            throw new NotFoundHttpException('Media file not found.');
        }

        return $stream;
    }

    private function resolveFileSize(FilesystemAdapter $disk, string $path): ?int
    {
        try {
            $size = $disk->size($path);
        } catch (Throwable) {
            return null;
        }

        return is_int($size) && $size > 0 ? $size : null;
    }
}
