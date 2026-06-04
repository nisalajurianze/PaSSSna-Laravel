<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=30 : Delete notifications older than this many days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = Notification::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$count} notifications older than {$days} days.");

        return Command::SUCCESS;
    }
}
