<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;


class AdminController extends Controller
{
  // In your Admin Controller
public function dashboard()
{
    // User statistics
    $userCounts = [
        'total' => User::count(),
        'students' => User::where('user_role', 'Student')->count(),
        'technicians' => User::where('user_role', 'Technician')->count(),
        'admins' => User::where('user_role', 'Admin')->count(),
    ];

    // Task statistics
    $taskCounts = [
        'total' => Task::count(),
        'completed' => Task::where('issue_status', 'Completed')->count(),
        'in_progress' => Task::where('issue_status', 'In Progress')->count(),
        'pending' => Task::where('issue_status', 'Pending')->count(),
    ];

    // Recent activities
    $recentTasks = Task::with('assignee')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

    // Technician performance
    $topTechnicians = User::where('user_role', 'Technician')
                        ->withCount(['tasks as completed_tasks' => function($query) {
                            $query->where('issue_status', 'Completed');
                        }])
                        ->orderByDesc('completed_tasks')
                        ->take(3)
                        ->get();

    return view('admin.dashboard', compact(
        'userCounts',
        'taskCounts',
        'recentTasks',
        'topTechnicians'
    ));
}

  
}
