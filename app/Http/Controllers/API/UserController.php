<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\UserData;
use App\Http\Requests\UserRequests\UserFilterRequest;
use App\Http\Requests\UserRequests\UserStoreRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UserController
{
    public function __construct(private UserService $userService) {}

    /**
     * Get a paginated list of users with optional filtering.
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
     */
    public function show(User $user): UserResource
    {
        return UserResource::make($user->load('media'))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing user.
     *
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
     */
    public function destroy(User $user): UserResource
    {
        $user->delete();

        return UserResource::make($user)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
