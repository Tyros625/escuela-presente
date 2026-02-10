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
        return $this->from(config('mail.from.address', 'noreply@escuelapresente.com'), config('mail.from.name', 'Escuela Presente'))
            ->view('email-tenant-created')
            ->with([
                'input' => $this->input,
            ])
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->subject('Cuenta creada - Escuela Presente');
    }
}
