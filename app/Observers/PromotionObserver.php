<?php

namespace App\Observers;

use App\Events\PromotionUpdated;
use App\Models\Promotion;

class PromotionObserver
{
    /**
     * Handle the Promotion "created" event.
     */
    public function created(Promotion $promotion): void
    {
        event(new PromotionUpdated('created', $promotion->fresh()));
    }

    /**
     * Handle the Promotion "updated" event.
     */
    public function updated(Promotion $promotion): void
    {
        // Check if is_active changed
        if ($promotion->isDirty('is_active')) {
            $action = $promotion->is_active ? 'activated' : 'deactivated';
            event(new PromotionUpdated($action, $promotion->fresh()));
        } else {
            event(new PromotionUpdated('updated', $promotion->fresh()));
        }
    }

    /**
     * Handle the Promotion "deleted" event.
     */
    public function deleted(Promotion $promotion): void
    {
        event(new PromotionUpdated('deleted', [
            'id' => $promotion->id,
            'name' => $promotion->name,
        ]));
    }

    /**
     * Handle the Promotion "restored" event.
     */
    public function restored(Promotion $promotion): void
    {
        event(new PromotionUpdated('restored', $promotion->fresh()));
    }
}
