<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';
    protected $description = 'Cancel unpaid bookings whose hold has expired';

    public function handle(BookingService $bookings): int
    {
        $count = $bookings->expireStale();
        $this->info("Expired {$count} booking(s).");

        return self::SUCCESS;
    }
}
