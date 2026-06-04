<?php

namespace App\Observers;

use App\Events\InventoryUpdated;
use App\Models\Inventory;

class InventoryObserver
{
    /**
     * Handle the Inventory "created" event.
     */
    public function created(Inventory $inventory): void
    {
        event(new InventoryUpdated($inventory, 'created'));
    }

    /**
     * Handle the Inventory "updated" event.
     */
    public function updated(Inventory $inventory): void
    {
        $action = 'updated';
        $previousStock = $inventory->getOriginal('quantity');
        $newStock = $inventory->quantity;

        // Check for low stock or out of stock
        if ($previousStock > 0 && $newStock === 0) {
            $action = 'out_of_stock';
        } elseif ($newStock <= 10 && $previousStock > 10) {
            $action = 'low_stock';
        }

        event(new InventoryUpdated($inventory, $action, $previousStock, $newStock));
    }

    /**
     * Handle the Inventory "deleted" event.
     */
    public function deleted(Inventory $inventory): void
    {
        event(new InventoryUpdated($inventory, 'deleted'));
    }

    /**
     * Handle the Inventory "restored" event.
     */
    public function restored(Inventory $inventory): void
    {
        event(new InventoryUpdated($inventory, 'restored'));
    }
}
