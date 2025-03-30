<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\Comment;
class HomeController extends Controller
{
    //
    public function index()
    {
        return view('home');
    }
    // Delete a comment
    public function destroy(Comment $comment)
    {
        // Ensure the authenticated user owns the comment
        if (auth()->id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
        }

        $comment->delete();
        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }

    // Update a comment
    public function update(Request $request, Comment $comment)
    {
        // Ensure the authenticated user owns the comment
        if (auth()->id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to edit this comment.');
        }

        // Validate the request
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        // Update the comment
        $comment->update([
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment updated successfully.');
    }
}
