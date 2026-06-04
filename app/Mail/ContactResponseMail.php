<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The subject of the email.
     *
     * @var string
     */
    public string $subject;

    /**
     * The message content.
     *
     * @var string
     */
    public string $message;

    /**
     * The recipient's name.
     *
     * @var string
     */
    public string $recipientName;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $message
     * @param string $recipientName
     */
    public function __construct(string $subject, string $message, string $recipientName)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->recipientName = $recipientName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.contact-response')
            ->with([
                'recipientName' => $this->recipientName,
                'message' => $this->message,
            ]);
    }
}
