<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChronicMedications;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\AuthorizesByPermissionGroup;

final class ChronicMedicationsPolicy
{
    use AuthorizesByPermissionGroup;

    public function viewAny(User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function view(User $user, ChronicMedications $chronicMedications): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->authorizeAction($user, 'create');
    }

    public function update(User $user, ChronicMedications $chronicMedications): bool
    {
        return $this->authorizeAction($user, 'update');
    }

    public function delete(User $user, ChronicMedications $chronicMedications): bool
    {
        return $this->authorizeAction($user, 'delete');
    }
}
