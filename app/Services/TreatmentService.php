<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\TreatmentData;
use App\Models\Treatment;
use Illuminate\Support\Facades\DB;
use Throwable;

final class TreatmentService
{
    /**
     * Store treatment data.
     *
     * @throws Throwable
     */
    public function store(TreatmentData $data): Treatment
    {
        return DB::transaction(static function () use ($data) {
            return Treatment::create($data->onlyModelAttributes());
        });
    }

    /**
     * Update treatment data.
     *
     * @throws Throwable
     */
    public function update(TreatmentData $data, Treatment $treatment): Treatment
    {
        return DB::transaction(static function () use ($data, $treatment) {
            return tap($treatment)->update($data->onlyModelAttributes());
        });
    }
}
