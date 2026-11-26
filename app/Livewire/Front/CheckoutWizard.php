<?php

namespace App\Livewire\Front;

use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class CheckoutWizard extends Component
{
    use WithFileUploads;

    public Room $room;
    public int $step = 1;

    public string $check_in = '';
    public string $check_out = '';
    public int $guests = 2;

    public string $customer_name = '';
    public string $phone = '';
    public string $notes = '';
    public string $payment_method = 'bank_transfer';

    public ?Booking $booking = null;
    public ?Payment $payment = null;
    public $proof;

    public function mount(Room $room): void
    {
        $this->room = $room->load('hotel', 'roomType');
        $this->check_in = (string) request('check_in', Carbon::today()->toDateString());
        $this->check_out = (string) request('check_out', Carbon::tomorrow()->toDateString());
        $this->guests = (int) request('guests', 2);
        $this->customer_name = auth()->user()->name;
        $this->phone = auth()->user()->phone ?? '';

        if (Carbon::parse($this->check_out)->lte(Carbon::parse($this->check_in))) {
            $this->check_out = Carbon::parse($this->check_in)->addDay()->toDateString();
        }
    }

    public function nights(): int
    {
        return max(1, Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out)));
    }

    public function total(): float
    {
        $tax = (float) config('booking.tax_percent', 0);

        return (float) $this->room->price * $this->nights() * (1 + $tax / 100);
    }

    public function next(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'check_in' => ['required', 'date', 'after_or_equal:today'],
                'check_out' => ['required', 'date', 'after:check_in'],
                'guests' => ['required', 'integer', 'min:1', 'max:'.$this->room->roomType->capacity],
            ]);

            if ($this->nights() > (int) config('booking.max_nights', 30)) {
                $this->addError('check_out', 'Maximum stay is 30 nights.');

                return;
            }

            $this->step = 2;

            return;
        }

        if ($this->step === 2) {
            $this->validate([
                'customer_name' => ['required', 'string', 'max:120'],
                'phone' => ['required', 'string', 'regex:/^62[0-9]{8,13}$/'],
                'notes' => ['nullable', 'string', 'max:500'],
            ], [
                'phone.regex' => 'Use WhatsApp format starting with 62.',
            ]);

            $this->step = 3;
        }
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function pay(BookingService $bookings): void
    {
        $this->validate([
            'payment_method' => ['required', 'in:bank_transfer,ewallet_sim'],
        ]);

        try {
            $booking = $bookings->createPending([
                'id_room' => $this->room->id,
                'check_in' => $this->check_in,
                'check_out' => $this->check_out,
                'guest_count' => $this->guests,
                'customer_name' => $this->customer_name,
                'notes' => $this->notes ?: null,
            ], auth()->user(), $this->payment_method);

            $this->booking = $booking;
            $this->payment = $booking->payments->first();
            $this->step = 4;

            if ($this->payment_method === 'ewallet_sim') {
                app(PaymentService::class)->payWithEwallet($this->payment);
                $this->refreshBooking();
            }
        } catch (RoomNotAvailableException $e) {
            $this->addError('booking', 'Room was just booked. Choose another room at this hotel.');
        }
    }

    public function uploadProof(PaymentService $payments): void
    {
        $this->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        try {
            $payments->attachProof($this->payment, $this->proof);
            $this->proof = null;
            $this->refreshBooking();
            $this->dispatch('toast', message: 'Payment proof uploaded. The hotel will verify it shortly.');
        } catch (\RuntimeException $e) {
            $this->addError('proof', $e->getMessage());
        }
    }

    public function payNow(PaymentService $payments): void
    {
        try {
            $payments->payWithEwallet($this->payment);
            $this->refreshBooking();
        } catch (\RuntimeException $e) {
            $this->addError('booking', $e->getMessage());
        }
    }

    private function refreshBooking(): void
    {
        $this->booking = Booking::with('payments')->find($this->booking->id);
        $this->payment = $this->booking->payments()->latest()->first();
    }

    public function render()
    {
        return view('livewire.front.checkout-wizard', [
            'nights' => $this->nights(),
            'total' => $this->total(),
        ]);
    }
}
