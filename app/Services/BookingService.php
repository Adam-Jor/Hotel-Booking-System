<?php

namespace App\Services;

use App\Exceptions\RoomNotAvailableException;
use App\Jobs\SendWhatsappNotification;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(private AvailabilityService $availability)
    {
    }

    public function createPending(array $data, User $user, string $paymentMethod = 'bank_transfer'): Booking
    {
        $lockKey = 'booking:'.$user->id.':'.$data['id_room'].':'.$data['check_in'].':'.$data['check_out'];
        $lock = Cache::lock($lockKey, 10);

        if (! $lock->get()) {
            throw new RoomNotAvailableException('Your booking is already being processed.');
        }

        try {
            return DB::transaction(function () use ($data, $user, $paymentMethod) {
                $room = Room::with('roomType')->lockForUpdate()->findOrFail($data['id_room']);

                if ($room->status !== 'available' || $room->archived ||
                    $this->availability->hasConflict($room->id, $data['check_in'], $data['check_out'])) {
                    throw new RoomNotAvailableException();
                }

                $nights = $this->availability->nights($data['check_in'], $data['check_out']);
                $taxPercent = (float) config('booking.tax_percent', 0);
                $total = (int) round($room->price * $nights * (1 + $taxPercent / 100));

                $booking = Booking::create([
                    'booking_code' => $this->nextCode(),
                    'id_user' => $user->id,
                    'id_room' => $room->id,
                    'customer_name' => $data['customer_name'],
                    'guest_count' => $data['guest_count'],
                    'check_in_date' => $data['check_in'],
                    'check_out_date' => $data['check_out'],
                    'total_nights' => $nights,
                    'price_per_night' => $room->price,
                    'total_price' => $total,
                    'status' => 'pending',
                    'notes' => $data['notes'] ?? null,
                    'expires_at' => now()->addMinutes((int) config('booking.hold_minutes', 60)),
                ]);

                $booking->payments()->create([
                    'payment_method' => $paymentMethod,
                    'amount' => $total,
                    'reference_code' => $this->referenceCode(),
                    'status' => 'pending',
                ]);

                SendWhatsappNotification::dispatch($booking->id, 'created')->afterCommit();

                return $booking->load('payments', 'room.hotel', 'room.roomType');
            });
        } finally {
            $lock->release();
        }
    }

    public function cancel(Booking $booking, string $reason, string $by = 'customer'): void
    {
        if (! in_array($booking->status, ['pending', 'paid', 'draft'], true)) {
            throw new \RuntimeException('This booking can no longer be cancelled.');
        }

        if ($by === 'customer' && $booking->status !== 'pending') {
            throw new \RuntimeException('Paid bookings can only be cancelled by an admin.');
        }

        DB::transaction(function () use ($booking, $reason, $by) {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            $booking->payments()->where('status', 'pending')->update(['status' => 'failed', 'reject_reason' => 'Booking cancelled']);

            SendWhatsappNotification::dispatch($booking->id, 'cancelled')->afterCommit();
        });
    }

    public function transition(Booking $booking, string $to): void
    {
        $allowed = [
            'draft' => ['pending'],
            'pending' => ['paid', 'cancelled'],
            'paid' => ['completed', 'cancelled'],
        ];

        if (! in_array($to, $allowed[$booking->status] ?? [], true)) {
            throw new \RuntimeException("Cannot change status from {$booking->status} to {$to}.");
        }

        $booking->update(['status' => $to]);
    }

    public function completePast(): int
    {
        return Booking::where('status', 'paid')
            ->where('archived', 0)
            ->whereDate('check_out_date', '<', today())
            ->update(['status' => 'completed']);
    }

    public function expireStale(): int
    {
        $count = 0;

        Booking::where('status', 'pending')
            ->where('archived', 0)
            ->where('expires_at', '<=', now())
            ->with('payments')
            ->chunkById(100, function ($bookings) use (&$count) {
                foreach ($bookings as $booking) {
                    $booking->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancel_reason' => 'expired',
                    ]);
                    $booking->payments()->where('status', 'pending')->update(['status' => 'failed', 'reject_reason' => 'Booking expired']);
                    SendWhatsappNotification::dispatch($booking->id, 'expired')->afterCommit();
                    $count++;
                }
            });

        return $count;
    }

    public function nextCode(): string
    {
        $max = (int) Booking::max('id');

        return 'BKG-'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    public function referenceCode(): string
    {
        return 'VA-'.strtoupper(bin2hex(random_bytes(5)));
    }
}
