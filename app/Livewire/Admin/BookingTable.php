<?php

namespace App\Livewire\Admin;

use App\Jobs\SendWhatsappNotification;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\PaymentService;
use Livewire\Component;
use Livewire\WithPagination;

class BookingTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public ?int $detailId = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function openDetail(int $id): void
    {
        $this->detailId = $id;
    }

    public function markPaid(int $id, PaymentService $payments): void
    {
        $booking = Booking::with('payments')->findOrFail($id);

        if ($booking->status === 'paid') {
            return;
        }

        $payments->recordCash($booking, auth()->user());
        session()->flash('success', "{$booking->booking_code} marked as paid (cash).");
    }

    public function complete(int $id, BookingService $bookings): void
    {
        $booking = Booking::findOrFail($id);

        try {
            $bookings->transition($booking, 'completed');
            session()->flash('success', "{$booking->booking_code} completed.");
        } catch (\RuntimeException $e) {
            session()->flash('success', $e->getMessage());
        }
    }

    public function cancel(int $id, BookingService $bookings): void
    {
        $booking = Booking::findOrFail($id);

        try {
            $bookings->cancel($booking, 'admin', 'admin');
            session()->flash('success', "{$booking->booking_code} cancelled.");
        } catch (\RuntimeException $e) {
            session()->flash('success', $e->getMessage());
        }
    }

    public function resend(int $id): void
    {
        $booking = Booking::findOrFail($id);
        SendWhatsappNotification::dispatch($booking->id, 'created');
        session()->flash('success', "WhatsApp for {$booking->booking_code} queued.");
    }

    public function render()
    {
        $bookings = Booking::with(['room.hotel', 'room.roomType', 'payments', 'user'])
            ->where('archived', 0)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->search}%")
                    ->orWhere('customer_name', 'like', "%{$this->search}%")
                    ->orWhereHas('room.hotel', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.booking-table', [
            'bookings' => $bookings,
            'detail' => $this->detailId
                ? Booking::with(['room.hotel', 'room.roomType', 'payments.verifier', 'user'])->find($this->detailId)
                : null,
        ]);
    }
}
