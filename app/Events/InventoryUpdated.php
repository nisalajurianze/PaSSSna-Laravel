<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inventory;
    public $action;
    public $previousStock;
    public $newStock;

    /**
     * Create a new event instance.
     */
    public function __construct($inventory, string $action, ?int $previousStock = null, ?int $newStock = null)
    {
        $this->inventory = $inventory;
        $this->action = $action; // created, updated, low_stock, out_of_stock
        $this->previousStock = $previousStock;
        $this->newStock = $newStock;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('restaurant'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $data = [
            'inventory_id' => $this->inventory->id,
            'action' => $this->action,
            'inventory' => $this->inventory,
            'timestamp' => now()->toIso8601String(),
        ];

        if ($this->previousStock !== null && $this->newStock !== null) {
            $data['previous_stock'] = $this->previousStock;
            $data['new_stock'] = $this->newStock;
        }

        return $data;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'inventory.updated';
    }
}
