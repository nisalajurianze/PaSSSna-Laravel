<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReport extends Mailable
{
    use Queueable, SerializesModels;

    public $stats;
    public $filePath;

    public function __construct($stats, $filePath)
    {
        $this->stats = $stats;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('Daily Report - ' . date('F d, Y'))
            ->view('emails.daily-report')
            ->with([
                'stats' => $this->stats,
                'date' => date('F d, Y'),
            ])
            ->attach($this->filePath, [
                'as' => 'daily-report-' . date('Y-m-d') . '.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
