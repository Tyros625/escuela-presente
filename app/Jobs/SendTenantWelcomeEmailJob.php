<?php

namespace App\Jobs;

use App\Mail\EmailForQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTenantWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300];

    public function __construct(
        public string $to,
        public array $emailData
    ) {}

    public function handle(): void
    {
        try {
            $mailable = new EmailForQueue($this->emailData);
            Mail::to($this->to)->send($mailable);
        } catch (\Throwable $e) {
            Log::error('SendTenantWelcomeEmailJob failed.', [
                'to' => $this->to,
                'exception' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
