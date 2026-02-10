<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTenantRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Nuevo Cliente Registrado',
            'message' => "Se ha registrado un nuevo cliente: {$this->tenant->school_name}",
            'tenant_id' => $this->tenant->id,
            'tenant_name' => $this->tenant->school_name,
            'domain' => $this->tenant->domain,
            'email' => $this->tenant->email,
            'href' => '/tenants',
            'icon' => 'fa fa-fw fa-building text-primary',
            'time' => now()->diffForHumans(),
        ];
    }
}
