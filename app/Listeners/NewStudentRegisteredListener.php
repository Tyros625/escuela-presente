<?php

namespace App\Listeners;

use App\Events\NewStudentRegisteredEvent;
use App\Notifications\NewStudentRegisteredNotification;
use Illuminate\Support\Facades\Notification;

class NewStudentRegisteredListener
{
    public function __construct()
    {
        //
    }

    public function handle(NewStudentRegisteredEvent $event)
    {
        Notification::send('jlsc92@gmail.com', new NewStudentRegisteredNotification($event->student));
    }
}
