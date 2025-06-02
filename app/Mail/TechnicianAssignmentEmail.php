<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TechnicianAssignmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $issue;
    public $task;
    public $technician;
    public $reporter;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($issue, $task, $technician, $reporter)
    {
        $this->issue = $issue;
        $this->task = $task;
        $this->technician = $technician;
        $this->reporter = $reporter;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('[Action Required] New Task Assignment - Issue #' . $this->issue->issue_id)
            ->view('emails.technician_assignment') 
            ->with([
                'issue' => $this->issue,
                'task' => $this->task,
                'technician' => $this->technician,
                'reporter' => $this->reporter,
                'location' => $this->issue->location,
                'taskUrl' => route('technician.task_details', $this->issue->issue_id),
            ]);
    }
}
