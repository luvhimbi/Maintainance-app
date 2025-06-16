<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Issue;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class IssueUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issue;
    public $task;
    public $update;
    public $updater;
    public $location;

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
        $this->location = sprintf(
            "%s, Floor %s, Room %s",
            $issue->building->building_name,
            $issue->floor->floor_number,
            $issue->room->room_number
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Issue Update: {$this->issue->issue_type} - {$this->update->status_change}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.issue-update',
            with: [
                'issue' => $this->issue,
                'task' => $this->task,
                'update' => $this->update,
                'updater' => $this->updater,
                'location' => $this->location,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
