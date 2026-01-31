<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\BillingData;
use App\Models\Billing;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BillingService
{
    /**
     * @throws Throwable
     */
    public function store(BillingData $data): Billing
    {
        return DB::transaction(static function () use ($data) {
            return Billing::create($data->onlyModelAttributes());
        });
    }

    /**
     * @throws Throwable
     */
    public function update(BillingData $data, Billing $billing): Billing
    {
        return DB::transaction(static function () use ($data, $billing) {
            tap($billing)->update($data->onlyModelAttributes());

            return $billing;
        });
    }
}
