<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public function download(Booking $booking)
    {
        $booking->load('user', 'room.hotel', 'room.roomType', 'payments');

        return Pdf::loadView('bookings.invoice', ['booking' => $booking])
            ->setPaper('a4')
            ->download("invoice-{$booking->booking_code}.pdf");
    }
}
