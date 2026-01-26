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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UserController
{
    use AuthorizesRequests;

    public function __construct(private UserService $userService) {}

    /**
     * Get a paginated list of users with optional filtering.
     *
     * @return {{ AnonymousResourceCollection<resource> }}
     */
    public function index(UserFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $users = User::getQuery()
            ->paginate($request->input('perPage', 20));

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
        $this->authorize('create', User::class);

        $user = $this->userService->store(UserData::from($request->validated()));

        return UserResource::make($user->load(['media', 'roles']))
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific user by ID.
     */
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return UserResource::make($user->load(['media', 'roles']))
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing user.
     *
     * @throws Throwable
     */
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        $updatedUser = $this->userService->update(UserData::from($request->validated()), $user);

        return UserResource::make($updatedUser->load(['media', 'roles']))
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): UserResource
    {
        $this->authorize('delete', $user);

        $user->delete();

        return UserResource::make($user)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}
