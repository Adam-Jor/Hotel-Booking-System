<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    public function hasConflict(int $roomId, string $checkIn, string $checkOut, ?int $ignoreBookingId = null): bool
    {
        return Booking::where('id_room', $roomId)
            ->where('archived', 0)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->where(function ($q) {
                $q->where('status', 'paid')
                    ->orWhere(fn ($q) => $q->where('status', 'pending')
                        ->where('expires_at', '>', now()));
            })
            ->exists();
    }

    public function availableRoomsQuery(int $hotelId, string $checkIn, string $checkOut)
    {
        return Room::query()
            ->where('id_hotel', $hotelId)
            ->where('archived', 0)
            ->where('status', 'available')
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->where('archived', 0)
                    ->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn)
                    ->where(function ($q) {
                        $q->where('status', 'paid')
                            ->orWhere(fn ($q) => $q->where('status', 'pending')
                                ->where('expires_at', '>', now()));
                    });
            });
    }

    public function availableRoomCount(int $hotelId, string $checkIn, string $checkOut): int
    {
        return $this->availableRoomsQuery($hotelId, $checkIn, $checkOut)->count();
    }

    /**
     * Hotels that have at least one free room for the range.
     */
    public function hotelAvailable(int $hotelId, ?string $checkIn, ?string $checkOut): bool
    {
        if (! $checkIn || ! $checkOut) {
            return Room::where('id_hotel', $hotelId)->where('archived', 0)->where('status', 'available')->exists();
        }

        return $this->availableRoomCount($hotelId, $checkIn, $checkOut) > 0;
    }

    public function nights(string $checkIn, string $checkOut): int
    {
        return Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
    }
}
