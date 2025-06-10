<?php

namespace App\Mail;

use App\Models\Issue;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IssueUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issue;
    public $task;
    public $update;
    public $updater;

    /**
     * Create a new message instance.
     *
     * @param Issue $issue
     * @param Task $task
     * @param TaskUpdate $update
     * @param User $updater
     */
    public function __construct(Issue $issue, Task $task, TaskUpdate $update, User $updater)
    {
        $this->issue = $issue;
        $this->task = $task;
        $this->update = $update;
        $this->updater = $updater;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Update on Your Reported Issue #' . $this->issue->issue_id)
            ->markdown('emails.issue_update')
            ->with([
                'issue' => $this->issue,
                'task' => $this->task,
                'update' => $this->update,
                'updater' => $this->updater,
            ]);
    }
}
