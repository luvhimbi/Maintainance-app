<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Issue;
class TaskAssignmentController extends Controller
{
    public function create()
{
    // Fetch all users with technician role
    $technicians = User::where('user_role', 'technician')->get();
    
    // Fetch tasks that need admin assignment (either unassigned or without admin)
    $tasks = Task::whereNull('admin_id')
                ->orWhere(function($query) {
                    $query->whereNull('assignee_id');
                })
                ->with(['issue', 'assignee']) // Eager load relationships
                ->get();
    
    return view('Admin.tasks.assign', compact('technicians', 'tasks'));
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'issue_id' => 'required|exists:issues,issue_id',
            'assignee_id' => 'required|exists:users,user_id',
            'expected_completion' => 'required|date',
            'priority' => 'required|in:Low,Medium,High',
        ]);

        $validated['admin_id'] = auth()->id();
        
        Task::create($validated);

        return redirect()->route('Admin.tasks.assign')->with('success', 'Task assigned successfully!');
    }
}
