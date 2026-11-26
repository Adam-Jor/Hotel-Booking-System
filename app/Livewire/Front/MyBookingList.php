<?php

namespace App\Livewire\Front;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Services\BookingService;
use App\Services\PaymentService;
use Livewire\Component;
use Livewire\WithFileUploads;

class MyBookingList extends Component
{
    use WithFileUploads;

    public string $status = 'all';
    public ?int $cancelId = null;
    public ?int $proofId = null;
    public ?int $reviewId = null;

    public $proof;
    public int $rating = 5;
    public string $comment = '';

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function confirmCancel(int $id): void
    {
        $this->cancelId = $id;
    }

    public function cancel(BookingService $bookings): void
    {
        $booking = Booking::where('id_user', auth()->id())->findOrFail($this->cancelId);

        try {
            $bookings->cancel($booking, 'customer');
            $this->dispatch('toast', message: "Booking {$booking->booking_code} cancelled.");
        } catch (\RuntimeException $e) {
            $this->dispatch('toast', message: $e->getMessage());
        }

        $this->cancelId = null;
    }

    public function openProof(int $id): void
    {
        $this->proofId = $id;
        $this->proof = null;
        $this->resetErrorBag();
    }

    public function uploadProof(PaymentService $payments): void
    {
        $this->validate(['proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048']]);

        $booking = Booking::where('id_user', auth()->id())->findOrFail($this->proofId);
        $payment = $booking->payments()->where('status', '!=', 'paid')->latest()->firstOrFail();

        try {
            $payments->attachProof($payment, $this->proof);
            $this->dispatch('toast', message: 'Proof uploaded. Waiting for verification.');
            $this->proofId = null;
            $this->proof = null;
        } catch (\RuntimeException $e) {
            $this->addError('proof', $e->getMessage());
        }
    }

    public function payNow(int $id, PaymentService $payments): void
    {
        $booking = Booking::where('id_user', auth()->id())->findOrFail($id);
        $payment = $booking->payments()->where('status', '!=', 'paid')->latest()->firstOrFail();

        try {
            $payments->payWithEwallet($payment);
            $this->dispatch('toast', message: 'Payment received. Your invoice is ready.');
        } catch (\RuntimeException $e) {
            $this->dispatch('toast', message: $e->getMessage());
        }
    }

    public function openReview(int $id): void
    {
        $this->reviewId = $id;
        $this->rating = 5;
        $this->comment = '';
    }

    public function submitReview(): void
    {
        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:600'],
        ]);

        $booking = Booking::where('id_user', auth()->id())->with('room')->findOrFail($this->reviewId);

        if ($booking->status !== 'completed' || $booking->review()->exists()) {
            $this->reviewId = null;

            return;
        }

        Review::create([
            'id_hotel' => $booking->room->id_hotel,
            'id_user' => auth()->id(),
            'id_booking' => $booking->id,
            'rating' => $this->rating,
            'comment' => $this->comment ?: null,
        ]);

        $this->dispatch('toast', message: 'Thanks for the review.');
        $this->reviewId = null;
    }

    public function render()
    {
        $bookings = Booking::with(['room.hotel', 'room.roomType', 'payments', 'review'])
            ->where('id_user', auth()->id())
            ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->get();

        return view('livewire.front.my-booking-list', compact('bookings'));
    }
}
