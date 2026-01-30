<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\MediaStoreData;
use App\Http\Requests\MediaRequests\MediaFilterRequest;
use App\Http\Requests\MediaRequests\MediaStoreRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class MediaController
{
    use AuthorizesRequests;

    public function __construct(private MediaService $mediaService) {}

    /**
     * @return AnonymousResourceCollection<int, MediaResource>
     */
    public function index(MediaFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Media::class);

        $media = Media::getQuery()
            ->paginate($request->input('perPage', 20));

        return MediaResource::collection($media)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * @throws Throwable
     */
    public function store(MediaStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Media::class);

        $media = $this->mediaService->store(MediaStoreData::from($request->validated()));

        return MediaResource::collection($media)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Media $media): MediaResource
    {
        $this->authorize('view', $media);

        return MediaResource::make($media)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    public function destroy(Media $media): MediaResource
    {
        $this->authorize('delete', $media);

        $media->delete();

        return MediaResource::make($media)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
