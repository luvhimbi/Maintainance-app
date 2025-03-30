<?php

namespace App\Notifications; // Correct namespace

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your profile was updated successfully',
            'url' => route('profile.edit'),
            'icon' => 'fas fa-user-edit',
            'time' => now()->toDateTimeString()
        ];
    }
}