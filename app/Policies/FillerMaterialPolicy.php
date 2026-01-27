<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FillerMaterial;
use App\Models\User;

final class FillerMaterialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('filler-materials.viewAny');
    }

    public function view(User $user, FillerMaterial $fillerMaterial): bool
    {
        return $user->can('filler-materials.view');
    }

    public function create(User $user): bool
    {
        return $user->can('filler-materials.create');
    }

    public function update(User $user, FillerMaterial $fillerMaterial): bool
    {
        return $user->can('filler-materials.update');
    }

    public function delete(User $user, FillerMaterial $fillerMaterial): bool
    {
        return $user->can('filler-materials.delete');
    }
}
