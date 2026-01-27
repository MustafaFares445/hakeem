<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChronicDiseases;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\AuthorizesByPermissionGroup;

final class ChronicDiseasesPolicy
{
    use AuthorizesByPermissionGroup;

    public function viewAny(User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function view(User $user, ChronicDiseases $chronicDiseases): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->authorizeAction($user, 'create');
    }

    public function update(User $user, ChronicDiseases $chronicDiseases): bool
    {
        return $this->authorizeAction($user, 'update');
    }

    public function delete(User $user, ChronicDiseases $chronicDiseases): bool
    {
        return $this->authorizeAction($user, 'delete');
    }
}
