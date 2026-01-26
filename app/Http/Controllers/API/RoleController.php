<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

final readonly class RoleController
{
    public function index(): AnonymousResourceCollection
    {
        return RoleResource::collection(
            Role::query()
                ->where('name' , '!=' , config('permission.dashboard_role'))
                ->get()
        );
    }
}
