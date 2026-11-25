<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendWhatsappNotification;
use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController
{
    use AuthorizesRequests;

    public function index()
    {
        return view('admin.bookings.index');
    }

    public function invoice(Booking $booking, InvoiceService $invoice)
    {
        $this->authorize('view', $booking);

        return $invoice->download($booking);
    }

    public function resendWhatsapp(Booking $booking)
    {
        SendWhatsappNotification::dispatch($booking->id, 'created');

        return back()->with('success', "WhatsApp for {$booking->booking_code} queued.");
    }
}
