<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function build()
    {
        if ($this->mailData['type'] === 'payment.created') {
            return $this->subject($this->mailData['title'])
                ->view('emails.demoMail');
        } elseif ($this->mailData['type'] === 'payment.updated') {
            return $this->subject($this->mailData['title'])
                ->view('emails.demoMail2');
        } else {
            return $this->subject($this->mailData['title'])
                ->view('emails.demoMail3');
        }
    }
}
