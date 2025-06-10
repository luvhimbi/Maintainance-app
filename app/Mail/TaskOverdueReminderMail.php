<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskOverdueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject('Reminder: Overdue Task #' . $this->task->task_id)
            ->markdown('emails.task_overdue_reminder')
            ->with(['task' => $this->task]);
    }
}
