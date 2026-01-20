<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\UserRequests\UserBulkDeleteRequest;
use App\Http\Requests\UserRequests\UserBulkStoreRequest;
use App\Http\Requests\UserRequests\UserBulkUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserBulkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;

final class UserBulkController
{
    public function __construct(private UserBulkService $userBulkService) {}

    public function store(UserBulkStoreRequest $request): JsonResponse
    {
        $users = $this->userBulkService->store($request->validated('items'));

        return UserResource::collection($users)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UserBulkUpdateRequest $request): AnonymousResourceCollection
    {
        $users = $this->userBulkService->update($request->validated('items'));

        return UserResource::collection($users)->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    public function destroy(UserBulkDeleteRequest $request): JsonResponse
    {
        $count = $this->userBulkService->delete($request->validated('ids'));

        return response()->json(['message' => ResponseMessages::DELETED->message(), 'deleted_count' => $count], Response::HTTP_OK);
    }
}
