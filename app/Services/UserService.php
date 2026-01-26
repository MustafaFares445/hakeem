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
            $user = User::create($data->onlyModelAttributes() + ['password' => Hash::make($data->password ?? Str::random(12))]);

            $user->assignRole($data->roles);

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

            if ($data->roles) {
                $user->syncRoles($data->roles);
            }

            MediaHelper::updateMedia($data->primaryImage, $user, 'primary-image');

            return $user;
        });
    }
}
