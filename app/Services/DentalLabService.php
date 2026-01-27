<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\DentalLabData;
use App\Models\DentalLab;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DentalLabService
{
    /**
     * Store dental lab data.
     *
     * @throws Throwable
     */
    public function store(DentalLabData $data): DentalLab
    {
        return DB::transaction(static function () use ($data) {
            return DentalLab::create($data->onlyModelAttributes());
        });
    }

    /**
     * Update dental lab data.
     *
     * @throws Throwable
     */
    public function update(DentalLabData $data, DentalLab $dentalLab): DentalLab
    {
        return DB::transaction(static function () use ($data, $dentalLab) {
            return tap($dentalLab)->update($data->onlyModelAttributes());
        });
    }
}
