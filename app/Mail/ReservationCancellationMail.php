<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The reservation instance.
     *
     * @var Reservation
     */
    public Reservation $reservation;

    /**
     * Create a new message instance.
     *
     * @param Reservation $reservation
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->reservation->status === \App\Models\Reservation::STATUS_REJECTED
            ? 'Reservation Rejected - PaSSSna Restaurant'
            : 'Reservation Cancelled - PaSSSna Restaurant';

        return $this->subject($subject)
            ->view('emails.reservation-cancellation')
            ->with([
                'reservation' => $this->reservation,
            ]);
    }
}
