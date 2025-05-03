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
            ->orderBy('updated_at', 'desc')
            ->paginate(5); // Paginate with 10 items per page

        return view('Student.dashboard', compact('issues'));
    }

}
