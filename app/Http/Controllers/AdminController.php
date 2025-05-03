<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;


class AdminController extends Controller
{

public function dashboard()
{// User statistics
    $userCounts = [
        'total' => User::count(),
        'students' => User::where('user_role', 'Student')->count(),
        'technicians' => User::where('user_role', 'Technician')->count(),
        'admins' => User::where('user_role', 'Admin')->count(),
    ];

// Task statistics
    $totalTasks = Task::count();
    $completedTasks = Task::where('issue_status', 'Completed')->count();
    $inProgressTasks = Task::where('issue_status', 'In Progress')->count();
    $pendingTasks = Task::where('issue_status', 'Pending')->count();

// Calculate completion rate safely
    $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

    $taskCounts = [
        'total' => $totalTasks,
        'completed' => $completedTasks,
        'in_progress' => $inProgressTasks,
        'pending' => $pendingTasks,
        'completion_rate' => $completionRate,
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
