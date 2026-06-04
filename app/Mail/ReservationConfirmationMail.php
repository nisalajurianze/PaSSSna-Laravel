<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmationMail extends Mailable
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
        return $this->subject('Reservation Confirmed - PaSSSna Restaurant')
            ->view('emails.reservation-confirmation')
            ->with([
                'reservation' => $this->reservation,
            ]);
    }
}
