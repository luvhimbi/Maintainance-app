<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Issue;
use App\Models\Comment;

class StudentController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();
    
        // Fetch paginated issues where the reporter_id matches the authenticated user's ID
        // and the status is not 'Resolved' or 'Closed'
        $issues = Issue::where('reporter_id', $userId)
            ->where('issue_status', '!=', 'Resolved')
            ->where('issue_status', '!=', 'Closed')
            ->with('location') // Eager load the location relationship
            ->paginate(5); // Paginate with 10 items per page
    
        return view('Student.dashboard', compact('issues'));
    }
    public function storeComment(Request $request, $issueId)
{
    $request->validate([
        'comment' => 'required|string|max:1000',
    ]);

    Comment::create([
        'issue_id' => $issueId,
        'user_id' => auth()->id(),
        'comment' => $request->input('comment'),
    ]);

    return redirect()->back()->with('success', 'Comment added successfully!');
}
}
