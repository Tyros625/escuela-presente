<?php

namespace App\Jobs;

use App\Mail\EmailForQueue;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class CreateTenantPublic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $input;

    protected $subdomain;

    public function __construct($input, $subdomain)
    {
        $this->input = $input;
        $this->subdomain = $subdomain;
    }

    public function handle()
    {
        $this->createTenant($this->input, $this->subdomain);

        $email = new EmailForQueue($this->input);
        Mail::to($this->input['email'])->send($email);
    }

    private function createTenant($input, $subdomain)
    {
        $now = Carbon::now();
        $accessEnd = $now->copy()->addMonth();

        $tenant = Tenant::create([
            'id' => $subdomain,
            'school_name' => $input['school_name'],
            'cct' => $input['cct'],
            'domain' => $input['domain'],
            'email' => $input['email'],
            'password' => $input['password'],
            'country_code' => $input['country_code'],
            'phone' => $input['phone'],
            'access_start' => $now->toDateString(),
            'access_end' => $accessEnd->toDateString(),
        ]);

        $tenant->createDomain(['domain' => $input['domain']]);
    }
}
