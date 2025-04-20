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

    public function markAllAsRead(Request $request)
    {
        try {
            // Get the authenticated user
            $user = auth()->user();
            
            // Mark all unread notifications as read
            $user->unreadNotifications->markAsRead();
            
            // Return success response
            return redirect()->back()->with('success', 'All notifications marked as read');
            
        } catch (\Exception $e) {
            // Log the error if needed
            \Log::error('Error marking notifications as read: ' . $e->getMessage());
            
            // Return error response
            return redirect()->back()->with('error', 'Failed to mark notifications as read');
        }
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

    /**
     * Show notifications for the technician and mark them as read when viewed.
     */
    public function indexTechnician()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You need to log in first.');
        }

        // Get notifications paginated
        $notifications = auth()->user()->notifications()->paginate(10);

        // Mark all unread notifications as read
        auth()->user()->unreadNotifications->markAsRead();

        return view('Technician.notifications', compact('notifications'));
    }

/*
    * Show a specific notification and mark it as read when viewed.
*/
    public function show($id)
{
    $notification = auth()->user()->notifications()->findOrFail($id);
    
    // Mark as read when viewed
    if ($notification->unread()) {
        $notification->markAsRead();
    }
    
    return view('Student.show', compact('notification'));
}

/**
 * Show a specific notification for the technician and mark it as read when viewed.
 */
public function showTechnician($id)
{
    $notification = auth()->user()->notifications()->findOrFail($id);
    
    // Mark as read when viewed
    if ($notification->unread()) {
        $notification->markAsRead();
    }
    
    return view('Technician.show', compact('notification'));
}

public function destroy($id)
{
    $notification = auth()->user()->notifications()->findOrFail($id);
    $notification->delete();
    
    return redirect()->route('notifications.index')
        ->with('success', 'Notification deleted successfully');
}
}
