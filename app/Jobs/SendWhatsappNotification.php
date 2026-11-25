<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(public int $bookingId, public string $event)
    {
    }

    public function handle(WhatsappService $whatsapp): void
    {
        $booking = Booking::with('user', 'room.hotel', 'room.roomType')->find($this->bookingId);

        if ($booking) {
            $whatsapp->send($booking, $this->event);
        }
    }
}
