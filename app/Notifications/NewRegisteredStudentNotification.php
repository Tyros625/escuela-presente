<?php

namespace App\Notifications;

use App\Models\Tenants\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRegisteredStudentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo Estudiante Registrado')
            ->greeting('Hola')
            ->line('Un nuevo estudiante se ha registrado')
            ->action('Entrar al sistema', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
