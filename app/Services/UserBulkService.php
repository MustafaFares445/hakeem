<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Mrmarchone\LaravelAutoCrud\Services\BulkService;

final class UserBulkService extends BulkService
{
    protected string $modelClass = User::class;

    public function store(array $dataArray): Collection
    {
        $dataArrayWithPasswords = collect($dataArray)
            ->map(function (array $data) {
                $data['password'] ??= str()->random(32);

                return $data;
            })
            ->all();

        return parent::store($dataArrayWithPasswords);
    }

    public function transformToDataObjects(array $dataArray): Collection
    {
        return collect($dataArray)->map(fn (array $data) => UserData::from($data));
    }

    public function bulkUpdate(array|Collection $dataObjects, array|Collection $models): Collection
    {
        $data = $dataObjects instanceof Collection ? $dataObjects : collect($dataObjects);
        $modelsCollection = $models instanceof Collection ? $models : collect($models);

        if ($data->count() !== $modelsCollection->count()) {
            throw new InvalidArgumentException('The number of models and data objects must match');
        }

        return DB::transaction(function () use ($data, $modelsCollection) {
            $modelsCollection->values()->each(function (User $model, int $index) use ($data) {
                $dataObject = $data->get($index);

                $model->update($dataObject->onlyModelAttributes());
            });

            $modelsCollection->each->refresh();

            return $modelsCollection;
        });
    }

    public function validateStore(array $dataArray): void
    {
        parent::validateStore($dataArray);

        $emails = collect($dataArray)->pluck('email')->filter()->values();
        $duplicates = $emails->duplicates();

        if ($duplicates->isNotEmpty()) {
            throw ValidationException::withMessages([
                'data' => ["Duplicate email found in batch: {$duplicates->implode(', ')}"],
            ]);
        }

        $existing = User::whereIn('email', $emails->all())->pluck('email');

        if ($existing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'data' => ["email already exist: {$existing->implode(', ')}"],
            ]);
        }
    }

    public function validateUpdate(array $dataArray, Collection $existingModels): void
    {
        $emailsToUpdate = collect($dataArray)
            ->filter(fn (array $data) => array_key_exists('email', $data))
            ->pluck('email', 'id');

        $duplicates = $emailsToUpdate->duplicates();

        if ($duplicates->isNotEmpty()) {
            throw ValidationException::withMessages([
                'data' => ["Duplicate email found in batch: {$duplicates->implode(', ')}"],
            ]);
        }

        foreach ($emailsToUpdate as $id => $email) {
            $conflict = User::where('email', $email)
                ->where('id', '!=', $id)
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'data' => ["email '{$email}' is already taken by another record"],
                ]);
            }
        }
    }
}
