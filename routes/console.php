<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Tasks
Schedule::command('orders:cleanup')->daily();
Schedule::command('reservations:cleanup')->daily();
Schedule::command('inventory:check-low-stock')->dailyAt('09:00');
Schedule::command('reports:generate-daily')->dailyAt('23:59');
Schedule::command('promotions:expire')->dailyAt('00:00');
Schedule::command('backup:clean')->daily();

// Custom Commands
Schedule::command('send:reservation-reminders')->everyMinute();
Schedule::command('update:order-statuses')->everyFiveMinutes();
Schedule::command('generate:monthly-reports')->monthlyOn(1, '00:00');

// Queue Worker Monitoring
Schedule::command('queue:monitor')->everyMinute();
