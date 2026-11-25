<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsappNotification;
use App\Models\Booking;
use Illuminate\Console\Command;

class SendCheckinReminders extends Command
{
    protected $signature = 'bookings:remind';
    protected $description = 'Queue a WhatsApp reminder for paid bookings checking in tomorrow';

    public function handle(): int
    {
        $bookings = Booking::where('status', 'paid')
            ->where('archived', 0)
            ->whereDate('check_in_date', today()->addDay())
            ->get();

        $bookings->each(fn (Booking $booking) => SendWhatsappNotification::dispatch($booking->id, 'reminder'));

        $this->info("Queued {$bookings->count()} reminder(s).");

        return self::SUCCESS;
    }
}
