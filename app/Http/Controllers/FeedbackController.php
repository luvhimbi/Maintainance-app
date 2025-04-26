<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
class FeedbackController extends Controller
{
    public function store(Request $request, Issue $issue)
    {
        // Validate the request
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comments' => 'nullable|string|max:500'
        ]);

        // Ensure issue is resolved
        if ($issue->issue_status !== 'Resolved') {
            return back()->with('error', 'Feedback can only be submitted for resolved issues.');
        }

        // Ensure user hasn't already submitted feedback
        if ($issue->hasFeedbackFrom(Auth::user())) {
            return back()->with('error', 'You have already submitted feedback for this issue.');
        }

        // Create feedback
        Feedback::create([
            'issue_id' => $issue->issue_id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comments' => $request->comments
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }
}
