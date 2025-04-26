<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Issue;
use App\Models\TaskUpdate;
use App\Models\Comment;

class TechnicianController extends Controller
{
    
    public function dashboard()
{
    // Get the authenticated user's ID
    $userId = Auth::id();

    // Fetch tasks assigned to the logged-in technician
    $tasks = Task::where('assignee_id', $userId)->get();

    // Count tasks by status
    $pendingTasks = $tasks->where('issue_status', 'Pending')->count();
    $inProgressTasks = $tasks->where('issue_status', 'In Progress')->count();
    $completedTasks = $tasks->where('issue_status', 'Completed')->count();

    // Get overdue tasks (where expected completion date is past and status isn't Completed)
    $overdueTasks = $tasks->filter(function($task) {
        return $task->expected_completion->isPast() && $task->issue_status != 'Completed';
    });

    // Pass data to the view
    return view('technician.dashboard', [
        'tasks' => $tasks,
        'pendingTasks' => $pendingTasks,
        'inProgressTasks' => $inProgressTasks,
        'completedTasks' => $completedTasks,
        'overdueTasks' => $overdueTasks,
        'overdueCount' => $overdueTasks->count()
    ]);
}


public function directions()
    {
        $defaultLocation = [
            'lat' => -25.53978422415537, 
            'lng' => 28.098271679102634
        ];

        return view('technician.directions', [
            'mapboxAccessToken' => config('services.mapbox.access_token'),
            'mapboxStyle' => config('services.mapbox.style', 'mapbox://styles/mapbox/streets-v11'),
            'defaultLocation' => $defaultLocation
        ]);
    }

    public function getRoute(Request $request)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'profile' => 'required|in:walking,driving,cycling'
        ]);

        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/{$request->profile}/{$request->origin};{$request->destination}", [
            'geometries' => 'geojson',
            'steps' => 'true',
            'overview' => 'full',
            'access_token' => config('services.mapbox.access_token')
        ]);

        return $response->json();
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
