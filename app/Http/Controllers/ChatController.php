<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Events\NewMessage;
use Illuminate\Broadcasting\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Models\User;


class ChatController extends Controller
{
 public function index()
{

    
    $users = User::where('user_id', '!=', auth()->id())
        ->whereIn('user_role', ['Admin', 'Technician'])
        ->get();

    return view('technician.index', compact('users'));
}
  

 

 
    public function markAsRead($messageId)
{
    // Change ChatMessage to Message to be consistent
    $message = Message::findOrFail($messageId);
    if ($message->receiver_id == auth()->id()) {
        $message->update(['read' => true]);
    }
    return response()->json(['success' => true]);
}

public function getMessages($userId)
{
    $messages = Message::where(function($q) use ($userId) {
        $q->where('sender_id', auth()->id())
          ->where('receiver_id', $userId);
    })->orWhere(function($q) use ($userId) {
        $q->where('sender_id', $userId)
          ->where('receiver_id', auth()->id());
    })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
    
    // Return with success flag for consistent handling
    return response()->json(['success' => true, 'messages' => $messages]);
}

public function sendMessage(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|exists:users,user_id',
        'message' => 'required|string'
    ]);
    
    $message = Message::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $request->receiver_id,
        'message' => $request->message,
        'read' => false // Make sure this field exists in the messages table
    ]);
    $message->load(['sender', 'receiver']);
    
    broadcast(new NewMessage($message))->toOthers();
    
    // Return with success flag for consistent handling
    return response()->json(['success' => true, 'message' => $message]);
}
}
