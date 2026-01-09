<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\AuthorizesByPermissionGroup;

class UserPolicy
{
    use AuthorizesByPermissionGroup;

    public function viewAny(User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function view(User $user, User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->authorizeAction($user, 'create');
    }

    public function update(User $user, User $user): bool
    {
        return $this->authorizeAction($user, 'update');
    }

    public function delete(User $user, User $user): bool
    {
        return $this->authorizeAction($user, 'delete');
    }
}
