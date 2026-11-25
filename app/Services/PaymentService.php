<?php

namespace App\Services;

use App\Jobs\SendWhatsappNotification;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private AvailabilityService $availability, private ImageService $images)
    {
    }

    public function attachProof(Payment $payment, UploadedFile $file): void
    {
        $payment->load('booking');

        if ($payment->booking->isExpired()) {
            throw new \RuntimeException('This booking has expired. Please create a new booking.');
        }

        if ($payment->status === 'paid') {
            throw new \RuntimeException('This payment is already paid.');
        }

        $path = $this->images->storeProof($file, 'payments');

        $payment->update([
            'proof_url' => $path,
            'status' => 'pending',
            'reject_reason' => null,
        ]);
    }

    public function approve(Payment $payment, User $admin): void
    {
        DB::transaction(function () use ($payment, $admin) {
            $payment->load('booking.room');

            if ($payment->status === 'paid') {
                return;
            }

            $booking = $payment->booking;

            // Room may have been released while waiting for verification.
            $conflict = $this->availability->hasConflict(
                $booking->id_room,
                $booking->check_in_date->toDateString(),
                $booking->check_out_date->toDateString(),
                $booking->id
            );

            $booking->update([
                'status' => $conflict ? 'pending' : 'paid',
                'expires_at' => $conflict ? now()->addMinutes(60) : $booking->expires_at,
            ]);

            $payment->update([
                'status' => 'paid',
                'payment_date' => now(),
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            SendWhatsappNotification::dispatch($booking->id, 'paid')->afterCommit();
        });
    }

    public function reject(Payment $payment, User $admin, string $reason): void
    {
        DB::transaction(function () use ($payment, $admin, $reason) {
            $payment->update([
                'status' => 'failed',
                'reject_reason' => $reason,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            SendWhatsappNotification::dispatch($payment->id_booking, 'rejected')->afterCommit();
        });
    }

    public function payWithEwallet(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->load('booking');

            if ($payment->booking->isExpired()) {
                throw new \RuntimeException('This booking has expired. Please create a new booking.');
            }

            $payment->update([
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            $payment->booking->update(['status' => 'paid']);

            SendWhatsappNotification::dispatch($payment->id_booking, 'paid')->afterCommit();
        });
    }

    public function recordCash(Booking $booking, User $admin): Payment
    {
        return DB::transaction(function () use ($booking, $admin) {
            $payment = $booking->payments()->create([
                'payment_method' => 'cash',
                'amount' => $booking->total_price,
                'payment_date' => now(),
                'reference_code' => 'CASH-'.strtoupper(bin2hex(random_bytes(4))),
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'status' => 'paid',
            ]);

            $booking->update(['status' => 'paid']);

            return $payment;
        });
    }
}
