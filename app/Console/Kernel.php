<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckLowStock::class,
        Commands\GenerateDailyReport::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check low stock every day at 8 AM
        $schedule->command('inventory:check-low-stock')
            ->dailyAt('08:00')
            ->description('Check for low stock items')
            ->onOneServer();

        // Generate daily report at midnight
        $schedule->command('report:generate-daily')
            ->dailyAt('23:59')
            ->description('Generate daily restaurant report')
            ->onOneServer();

        // Backup database daily at 2 AM
        $schedule->command('db:backup')
            ->dailyAt('02:00')
            ->description('Backup restaurant database')
            ->onOneServer();

        // Clear old reservations (older than 30 days) weekly
        $schedule->command('reservations:cleanup')
            ->weekly()
            ->description('Clean up old reservations')
            ->onOneServer();

        // Send weekly report to admin every Monday at 9 AM
        $schedule->command('report:generate-weekly')
            ->weeklyOn(1, '09:00')
            ->description('Generate weekly report')
            ->onOneServer();

        // Check for expired promotions daily
        $schedule->command('promotions:check-expired')
            ->dailyAt('00:05')
            ->description('Check for expired promotions')
            ->onOneServer();

        // Generate monthly report on the first day of the month
        $schedule->command('report:generate-monthly')
            ->monthlyOn(1, '00:10')
            ->description('Generate monthly report')
            ->onOneServer();

        // Clean up old notifications (older than 30 days) monthly
        $schedule->command('notifications:cleanup')
            ->monthly()
            ->description('Clean up old notifications')
            ->onOneServer();

        // Send birthday wishes to customers at 10 AM daily
        $schedule->command('customers:send-birthday-wishes')
            ->dailyAt('10:00')
            ->description('Send birthday wishes to customers')
            ->onOneServer();

        // Update order statuses for long-pending orders hourly
        $schedule->command('orders:update-status')
            ->hourly()
            ->description('Update order statuses')
            ->onOneServer();

        // Generate end-of-day financial report at 11 PM
        $schedule->command('report:generate-eod')
            ->dailyAt('23:00')
            ->description('Generate end-of-day financial report')
            ->onOneServer();

        // Send reservation reminders daily at 9 AM for same-day reservations
        $schedule->command('reservations:send-reminders')
            ->dailyAt('09:00')
            ->description('Send reservation reminders')
            ->onOneServer();

        // Process payroll on the last day of the month
        $schedule->command('staff:process-payroll')
            ->monthlyOn(30, '00:00')
            ->description('Process staff payroll')
            ->onOneServer();

        // Enable output for all scheduled tasks in development
        if (app()->environment('local')) {
            $schedule->command('inventory:check-low-stock')
                ->everyMinute()
                ->sendOutputTo(storage_path('logs/schedule-inventory.log'));

            $schedule->command('report:generate-daily')
                ->everyMinute()
                ->sendOutputTo(storage_path('logs/schedule-report.log'));
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone()
    {
        return config('app.timezone', 'UTC');
    }
}
