<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mrmarchone\LaravelAutoCrud\Helpers\MediaHelper;
use Spatie\Permission\Models\Role;
use Throwable;

final class UserService
{
    /**
     * Validate user data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(UserData $data): User
    {
        return DB::transaction(static function () use ($data) {
            $attributes = $data->onlyModelAttributes();

            // Auto-generate password if not provided
            if (empty($attributes['password'])) {
                $attributes['password'] = Hash::make(Str::random(16));
            } else {
                $attributes['password'] = Hash::make($attributes['password']);
            }

            $user = User::create($attributes);

            // Assign roles if provided
            if (! empty($data->roles)) {
                $roles = Role::whereIn('name', $data->roles)->get();
                $user->syncRoles($roles);
            }

            MediaHelper::uploadMedia($data->primaryImage, $user, 'primary-image');

            return $user;
        });
    }

    /**
     * Update user data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(UserData $data, User $user): User
    {
        return DB::transaction(static function () use ($data, $user) {
            tap($user)->update($data->onlyModelAttributes());

            // Sync roles if provided
            if ($data->roles !== null) {
                if (empty($data->roles)) {
                    $user->syncRoles([]);
                } else {
                    $roles = Role::whereIn('name', $data->roles)->get();
                    $user->syncRoles($roles);
                }
            }

            MediaHelper::updateMedia($data->primaryImage, $user, 'primary-image');

            return $user;
        });
    }
}
