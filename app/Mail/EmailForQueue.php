<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForQueue extends Mailable
{
    use Queueable, SerializesModels;

    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function build()
    {
        return $this->view('email-tenant-created')
            ->with([
                'input' => $this->input,
            ])
            ->replyTo('jsanchez@hacktrick.tech', 'Juan Sánchez')
            ->subject('New Tenant Generated');
    }
}
