<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FillerMaterialData;
use App\Models\FillerMaterial;
use Illuminate\Support\Facades\DB;
use Throwable;

final class FillerMaterialService
{
    /**
     * @throws Throwable
     */
    public function store(FillerMaterialData $data): FillerMaterial
    {
        return DB::transaction(static function () use ($data): FillerMaterial {
            return FillerMaterial::create($data->onlyModelAttributes());
        });
    }

    /**
     * @throws Throwable
     */
    public function update(FillerMaterialData $data, FillerMaterial $fillerMaterial): FillerMaterial
    {
        return DB::transaction(static function () use ($data, $fillerMaterial): FillerMaterial {
            tap($fillerMaterial)->update($data->onlyModelAttributes());

            return $fillerMaterial;
        });
    }

    /**
     * @throws Throwable
     */
    public function delete(FillerMaterial $fillerMaterial): void
    {
        DB::transaction(static function () use ($fillerMaterial): void {
            $fillerMaterial->delete();
        });
    }
}
