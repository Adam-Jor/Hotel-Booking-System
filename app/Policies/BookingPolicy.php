<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->id_user === $user->id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return in_array($booking->status, ['pending', 'paid', 'draft'], true);
        }

        return $booking->id_user === $user->id && $booking->status === 'pending';
    }

    public function review(User $user, Booking $booking): bool
    {
        return $booking->id_user === $user->id
            && $booking->status === 'completed'
            && ! $booking->review()->exists();
    }
}
