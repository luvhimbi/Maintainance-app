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


// TechnicianController.php
public function directions()
{
    // TUT South Campus Coordinates (Block K)
    $defaultLocation = [
        'lat' =>  -25.53978422415537, 
        'lng' => 28.098271679102634
    ];
   // Campus buildings data with enhanced details
   $buildings = [
    [
        'name' => 'Block K (Main Building)',
        'position' => ['lat' => -25.5486, 'lng' => 28.1087],
        'icon' => 'https://maps.google.com/mapfiles/kml/pal3/icon21.png',
        'description' => 'Main administrative building housing key offices and lecture halls',
        'departments' => ['Administration', 'Finance', 'HR', 'Main Lecture Halls'],
        'facilities' => ['Reception', 'Cafeteria', 'Auditorium']
    ],
    [
        'name' => 'Swimming Pool',
        'position' => ['lat' => -25.541422705089307, 'lng' => 28.093935561610277],
        'icon' => 'https://maps.google.com/mapfiles/kml/pal3/icon49.png',
        'description' => 'Olympic-sized swimming pool with spectator seating',
        'facilities' => ['Changing rooms', 'First aid station', 'Pro shop']
    ],
    [
        'name' => 'Engineering Block',
        'position' => ['lat' => -25.5490, 'lng' => 28.1079],
        'icon' => 'https://maps.google.com/mapfiles/kml/pal3/icon7.png',
        'description' => 'Engineering faculty with labs and workshops',
        'departments' => ['Mechanical Engineering', 'Electrical Engineering', 'Civil Engineering'],
        'facilities' => ['Computer labs', 'Workshops', 'Research centers']
    ],
    [
        'name' => 'Student Center',
        'position' => ['lat' => -25.5478, 'lng' => 28.1082],
        'icon' => 'https://maps.google.com/mapfiles/kml/pal3/icon48.png',
        'description' => 'Hub for student activities and services',
        'facilities' => ['Student lounge', 'Cafeteria', 'Bookstore', 'ATM']
    ],
    [
        'name' => 'Sports Complex',
        'position' => ['lat' => -25.5472, 'lng' => 28.1075],
        'icon' => 'https://maps.google.com/mapfiles/kml/pal3/icon41.png',
        'description' => 'Indoor and outdoor sports facilities',
        'facilities' => ['Gym', 'Basketball courts', 'Soccer field', 'Changing rooms']
    ],
    [
        'name' => 'Library',
        'position' => ['lat' => -25.5483, 'lng' => 28.1078],
        'icon' => 'https://maps.google.com/mapfiles/kml/pal3/icon10.png',
        'description' => 'Main campus library with study spaces',
        'facilities' => ['Reading rooms', 'Computer lab', 'Group study areas']
    ]
];

return view('technician.directions', [
    'googleMapsApiKey' => config('services.google.maps_api_key'),
    'defaultLocation' => $defaultLocation,
    'buildings' => $buildings
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
