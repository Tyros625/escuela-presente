<?php

namespace App\Events;

use App\Models\Tenants\Student;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewStudentRegisteredEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
