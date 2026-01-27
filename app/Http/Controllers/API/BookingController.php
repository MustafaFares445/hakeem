<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Data\BookingData;
use App\Http\Requests\BookingRequests\BookingFilterRequest;
use App\Http\Requests\BookingRequests\BookingStoreRequest;
use App\Http\Requests\BookingRequests\BookingUpdateRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mrmarchone\LaravelAutoCrud\Enums\ResponseMessages;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class BookingController
{
    use AuthorizesRequests;

    public function __construct(private BookingService $bookingService) {}

    /**
     * Get a paginated list of bookings with optional filtering.
     *
     * @return AnonymousResourceCollection<BookingResource>
     */
    public function index(BookingFilterRequest $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = Booking::getQuery()
            ->paginate($request->input('perPage', 20));

        return BookingResource::collection($bookings)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Create a new booking.
     *
     * @throws Throwable
     */
    public function store(BookingStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Booking::class);

        $booking = $this->bookingService->store(BookingData::from($request->validated()));

        return BookingResource::make($booking)
            ->additional(['message' => ResponseMessages::CREATED->message()])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Get a specific booking by ID.
     */
    public function show(Booking $booking): BookingResource
    {
        $this->authorize('view', $booking);

        return BookingResource::make($booking)
            ->additional(['message' => ResponseMessages::RETRIEVED->message()]);
    }

    /**
     * Update an existing booking.
     *
     * @throws Throwable
     */
    public function update(BookingUpdateRequest $request, Booking $booking): BookingResource
    {
        $this->authorize('update', $booking);

        $updatedBooking = $this->bookingService->update(BookingData::from($request->validated()), $booking);

        return BookingResource::make($updatedBooking)
            ->additional(['message' => ResponseMessages::UPDATED->message()]);
    }

    /**
     * Delete a booking.
     */
    public function destroy(Booking $booking): BookingResource
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        return BookingResource::make($booking)
            ->additional(['message' => ResponseMessages::DELETED->message()]);
    }
}

