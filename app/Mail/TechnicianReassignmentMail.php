<?php

namespace App\Mail;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TechnicianReassignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issue;
    public $technician;
    public $oldType;
    public $newType;

    public function __construct(Issue $issue, User $technician, $oldType, $newType)
    {
        $this->issue = $issue;
        $this->technician = $technician;
        $this->oldType = $oldType;
        $this->newType = $newType;
    }

    public function build()
    {
        return $this->subject('Task Reassignment Notice - Issue #' . $this->issue->issue_id)
            ->view('emails.technician_reassignment_html')
            ->with([
                'issue' => $this->issue,
                'technician' => $this->technician,
                'oldType' => $this->oldType,
                'newType' => $this->newType,
            ]);
    }
}
