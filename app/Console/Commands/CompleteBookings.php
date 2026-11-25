<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class CompleteBookings extends Command
{
    protected $signature = 'bookings:complete';
    protected $description = 'Mark paid bookings as completed after check-out';

    public function handle(BookingService $bookings): int
    {
        $count = $bookings->completePast();
        $this->info("Completed {$count} booking(s).");

        return self::SUCCESS;
    }
}
