<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('bookings:expire')->everyFiveMinutes();
Schedule::command('bookings:complete')->dailyAt('01:00');
Schedule::command('bookings:remind')->dailyAt('09:00');
