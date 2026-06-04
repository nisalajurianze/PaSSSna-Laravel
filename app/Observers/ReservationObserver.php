<?php

namespace App\Observers;

use App\Events\ReservationStatusUpdated;
use App\Models\Reservation;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $reservation): void
    {
        event(new ReservationStatusUpdated($reservation, 'new', $reservation->status));
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $reservation): void
    {
        // Only broadcast if status changed
        if ($reservation->isDirty('status')) {
            $oldStatus = $reservation->getOriginal('status');
            $newStatus = $reservation->status;
            event(new ReservationStatusUpdated($reservation, $oldStatus, $newStatus));
        }
    }

    /**
     * Handle the Reservation "deleted" event.
     */
    public function deleted(Reservation $reservation): void
    {
        // Optionally broadcast deletion
    }

    /**
     * Handle the Reservation "restored" event.
     */
    public function restored(Reservation $reservation): void
    {
        event(new ReservationStatusUpdated($reservation, 'restored', $reservation->status));
    }
}
