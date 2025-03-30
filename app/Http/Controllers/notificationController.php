<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\DatabaseNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Send a notification to a specific user.
     */
    public function sendNotification()
    {
        $user = User::find(1);

        if ($user) {
            $user->notify(new DatabaseNotification('Hello, this is a test notification!'));
        }

        return redirect()->back()->with('success', 'Notification sent successfully!');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAsRead()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Show notifications and mark them as read when viewed.
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You need to log in first.');
        }

        // Get notifications paginated
        $notifications = auth()->user()->notifications()->paginate(10);

        // Mark all unread notifications as read
        auth()->user()->unreadNotifications->markAsRead();

        return view('Student.notifications', compact('notifications'));
    }
}
