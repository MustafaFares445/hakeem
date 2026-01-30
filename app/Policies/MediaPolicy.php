<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Media;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Mrmarchone\LaravelAutoCrud\Traits\AuthorizesByPermissionGroup;

final class MediaPolicy
{
    use AuthorizesByPermissionGroup;

    public function viewAny(User $user): bool
    {
        return $this->authorizeAction($user, 'view');
    }

    public function view(User $user, Media $media): bool
    {
        $model = $media->model;

        if ($model instanceof Patient) {
            return $user->can('view', $model);
        }

        if ($model instanceof MedicalRecord) {
            return $user->can('view', $model);
        }

        return $user->can('view', $model);
    }

    public function create(User $user): bool
    {
        return $this->authorizeAction($user, 'create');
    }

    public function update(User $user, Media $media): bool
    {
        $model = $media->model;

        if ($model instanceof Patient) {
            return $user->can('update', $model);
        }

        if ($model instanceof MedicalRecord) {
            return $user->can('update', $model);
        }

        return $user->can('update', $model);
    }

    public function delete(User $user, Media $media): bool
    {
        $model = $media->model;

        if ($model instanceof Patient) {
            return $user->can('delete', $model);
        }

        if ($model instanceof MedicalRecord) {
            return $user->can('delete', $model);
        }

        return $user->can('delete', $model);
    }
}
