<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Services\PaymentService;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $method = '';
    public ?int $rejectId = null;
    public string $rejectReason = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedMethod(): void { $this->resetPage(); }

    public function approve(int $id, PaymentService $payments): void
    {
        $payment = Payment::with('booking')->findOrFail($id);
        $payments->approve($payment, auth()->user());

        $this->dispatch('toast', message: "Payment {$payment->reference_code} approved.");
    }

    public function openReject(int $id): void
    {
        $this->rejectId = $id;
        $this->rejectReason = '';
        $this->resetErrorBag();
    }

    public function reject(PaymentService $payments): void
    {
        $this->validate([
            'rejectReason' => ['required', 'string', 'max:180'],
        ]);

        $payment = Payment::with('booking')->findOrFail($this->rejectId);
        $payments->reject($payment, auth()->user(), $this->rejectReason);

        $this->dispatch('toast', message: "Payment {$payment->reference_code} rejected.");
        $this->rejectId = null;
        $this->rejectReason = '';
    }

    public function render()
    {
        $payments = Payment::with(['booking.user', 'booking.room.hotel', 'verifier'])
            ->where('archived', 0)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('reference_code', 'like', "%{$this->search}%")
                    ->orWhereHas('booking', fn ($q) => $q->where('booking_code', 'like', "%{$this->search}%")
                        ->orWhere('customer_name', 'like', "%{$this->search}%"));
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->method, fn ($q) => $q->where('payment_method', $this->method))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.payment-table', compact('payments'));
    }
}
