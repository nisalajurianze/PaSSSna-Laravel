<?php

namespace App\Observers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Broadcast new order creation
        event(new OrderStatusUpdated($order, 'new', $order->status));
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Only broadcast if status changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            event(new OrderStatusUpdated($order, $oldStatus, $newStatus));
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // Optionally broadcast deletion
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        event(new OrderStatusUpdated($order, 'restored', $order->status));
    }
}
