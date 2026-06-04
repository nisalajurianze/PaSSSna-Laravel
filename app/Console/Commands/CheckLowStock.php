<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockNotification;
use Illuminate\Support\Facades\DB;


class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-low-stock';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check inventory for low stock items and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking for low stock items...');

        // Get all inventory items with low stock
        $lowStockItems = Inventory::query()
            ->where('is_active', true)
            ->whereRaw('current_quantity <= minimum_quantity')
            ->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('No low stock items found.');
            return;
        }

        $this->warn('Found ' . $lowStockItems->count() . ' low stock items.');

        $admins = User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->get(['id', 'email']);

        // Log low stock items
        foreach ($lowStockItems as $item) {
            $this->error('Low stock: ' . $item->name . ' - Current: ' . $item->current_quantity . ', Min: ' . $item->minimum_quantity);

            foreach ($admins as $admin) {
                $inventoryItem = $item instanceof Inventory ? $item : Inventory::find($item->id);
                if ($inventoryItem) {
                    Notification::createLowStockNotification($admin->id, $inventoryItem);
                }
            }

            $item->updateStatus();
        }

        // Send email notifications to admin
        $adminEmails = $admins->pluck('email')->filter()->unique()->values()->toArray();

        if (!empty($adminEmails)) {
            $data = [
                'items' => $lowStockItems,
                'total_items' => $lowStockItems->count(),
                'date' => now()->format('Y-m-d H:i:s'),
            ];

            foreach ($adminEmails as $email) {
                Mail::to($email)->send(new LowStockNotification($data));
                $this->info('Low stock notification sent to: ' . $email);
            }
        }

        $this->info('Low stock check completed successfully.');
    }
}
