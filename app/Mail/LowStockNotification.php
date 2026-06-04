<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Low Stock Alert - PaSSSna Restaurant')
            ->view('emails.low-stock')
            ->with([
                'items' => $this->data['items'],
                'date' => $this->data['date'],
                'total_items' => $this->data['total_items'],
            ]);
    }
}
