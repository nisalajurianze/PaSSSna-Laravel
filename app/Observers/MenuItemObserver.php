<?php

namespace App\Observers;

use App\Events\MenuItemUpdated;
use App\Models\MenuItem;

class MenuItemObserver
{
    /**
     * Handle the MenuItem "created" event.
     */
    public function created(MenuItem $menuItem): void
    {
        event(new MenuItemUpdated('created', $menuItem->fresh()));
    }

    /**
     * Handle the MenuItem "updated" event.
     */
    public function updated(MenuItem $menuItem): void
    {
        event(new MenuItemUpdated('updated', $menuItem->fresh()));
    }

    /**
     * Handle the MenuItem "deleted" event.
     */
    public function deleted(MenuItem $menuItem): void
    {
        event(new MenuItemUpdated('deleted', [
            'id' => $menuItem->id,
            'name' => $menuItem->name,
        ]));
    }

    /**
     * Handle the MenuItem "restored" event.
     */
    public function restored(MenuItem $menuItem): void
    {
        event(new MenuItemUpdated('restored', $menuItem->fresh()));
    }
}
