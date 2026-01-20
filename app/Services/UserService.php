<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Mrmarchone\LaravelAutoCrud\Helpers\MediaHelper;
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
            $attributes['password'] ??= str()->random(32);

            $user = User::create($attributes);

            MediaHelper::uploadMedia($data->primaryImage, $user, 'primary-image');
            MediaHelper::uploadMedia($data->images, $user, 'images');

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

            MediaHelper::updateMedia($data->primaryImage, $user, 'primary-image');
            MediaHelper::updateMedia($data->images, $user, 'images');

            return $user;
        });
    }
}
