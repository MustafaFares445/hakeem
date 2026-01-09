<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use App\Models\User;
use App\Data\UserData;
use App\Services\UserService;
use App\Http\Requests\UserRequests\UserStoreRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Http\Requests\UserRequests\UserBulkStoreRequest;
use App\Http\Requests\UserRequests\UserBulkUpdateRequest;
use App\Http\Requests\UserRequests\UserBulkDeleteRequest;
use App\Http\Resources\UserResource;
use App\Traits\FilterQueries\UserFilterQuery;
use App\Http\Requests\UserRequests\UserFilterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController
{
    public function __construct(protected UserService $userService) {}

    /**
     * Get a paginated list of users with optional filtering.
     *
     * @param UserFilterRequest $request
     * @return UserResource
     */
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $users = User::getQuery()
            ->paginate($request->get('perPage', 20));

        return UserResource::collection($users)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new user.
     *
     * @param UserStoreRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->userService->store(UserData::from($request->validated()));

        return UserResource::make($user->load('media', 'media'))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific user by ID.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return UserResource::make($user->load('media'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing user.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return UserResource
     * @throws Throwable
     */
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $updatedUser = $this->userService->update(UserData::from($request->validated()), $user);

        return UserResource::make($updatedUser->load('media', 'media'))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return UserResource
     */
    public function destroy(User $user): UserResource
    {
        $user->delete();

        return UserResource::make($user)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }

    /**
     * Create multiple users.
     *
     * @param UserBulkStoreRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function bulkStore(UserBulkStoreRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
    {
        try {
            $items = $request->validated()['items'];
            $users = [];
            
            foreach ($items as $item) {
                $users[] = User::create($item);
            }
            
            return UserResource::collection(collect($users))->additional(['message' => ResponseMessages::CREATED->message()]);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update multiple users.
     *
     * @param UserBulkUpdateRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function bulkUpdate(UserBulkUpdateRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
    {
        try {
            $items = $request->validated()['items'];
            $users = [];
            
            foreach ($items as $item) {
                $id = $item['id'];
                unset($item['id']);
                $user = User::findOrFail($id);
                $user->update($item);
                $users[] = $user->fresh();
            }
            
            return UserResource::collection(collect($users))->additional(['message' => ResponseMessages::UPDATED->message()]);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete multiple users.
     *
     * @param UserBulkDeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDestroy(UserBulkDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $ids = $request->validated()['ids'];
            User::whereIn('id', $ids)->delete();
            
            return response()->json(['message' => ResponseMessages::DELETED->message(), 'deleted_count' => count($ids)], \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(['error' => 'There is an error.'], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
