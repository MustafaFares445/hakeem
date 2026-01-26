<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\AuthorizesByPermissionGroup;

final class PatientPolicy
{
    use AuthorizesByPermissionGroup;

    public function viewAny(User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->authorizeAction($user, 'create');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $this->authorizeAction($user, 'update');
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $this->authorizeAction($user, 'delete');
    }
}
