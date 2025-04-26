<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DatabaseNotification extends Notification implements ShouldBroadcast
{
    use Queueable;



    protected $message;
    protected $actionUrl;

    public function __construct($message, $actionUrl)
    {
        $this->message = $message;
        $this->actionUrl = $actionUrl;
    }

   public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'user_id' => $notifiable->id,
        ];
    }
}
