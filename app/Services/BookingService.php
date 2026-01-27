<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\BookingData;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BookingService
{
    /**
     * Validate booking data.
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function store(BookingData $data): Booking
    {
        return DB::transaction(static function () use ($data) {
            $booking = Booking::create($data->onlyModelAttributes());

            return $booking;
        });
    }

    /**
     * Update booking data
     * Store to DB if there are no errors.
     *
     * @throws Throwable
     */
    public function update(BookingData $data, Booking $booking): Booking
    {
        return DB::transaction(static function () use ($data, $booking) {
            tap($booking)->update($data->onlyModelAttributes());

            return $booking;
        });
    }
}
