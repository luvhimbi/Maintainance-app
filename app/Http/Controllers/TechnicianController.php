<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\Comment;

class TechnicianController extends Controller
{public function dashboard()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Fetch tasks assigned to the logged-in technician
        $tasks = Task::where('assignee_id', $userId)->get();

        // Count tasks by status
        $pendingTasks = $tasks->where('issue_status', 'Pending')->count();
        $inProgressTasks = $tasks->where('issue_status', 'In Progress')->count();
        $completedTasks = $tasks->where('issue_status', 'Completed')->count();

        // Pass data to the view
        return view('technician.dashboard', [
            'tasks' => $tasks,
            'pendingTasks' => $pendingTasks,
            'inProgressTasks' => $inProgressTasks,
            'completedTasks' => $completedTasks,
        ]);
    }

    public function viewTaskDetails($task_id)
    {
        
        $task = Task::with([
                'issue.location', // Include issue and its location
                'issue.attachments', // Include issue attachments
                'assignee'
            
            ])
            ->findOrFail($task_id);

        // Ensure the authenticated user is the assignee
        if ($task->assignee_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('technician.task_details', compact('task'));
    }
}
