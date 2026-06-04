<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PromotionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action;
    public $promotion;

    /**
     * Create a new event instance.
     */
    public function __construct(string $action, $promotion)
    {
        $this->action = $action; // created, updated, deleted, activated, deactivated
        $this->promotion = $promotion;
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
        // Handle both array and object types for promotion
        if (is_array($this->promotion)) {
            $promotionId = $this->promotion['id'] ?? null;
            $promotionData = $this->promotion;
        } else {
            $promotionId = $this->promotion->id ?? null;
            $promotionData = $this->promotion;
        }

        return [
            'action' => $this->action,
            'promotion_id' => $promotionId,
            'promotion' => $promotionData,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'promotion.updated';
    }
}
