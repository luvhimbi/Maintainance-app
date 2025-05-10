<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;


class AdminController extends Controller
{

public function dashboard()
{
    // User statistics with availability status
    $technicianStatus = User::where('user_role', 'Technician')
        ->join('Technicians', 'users.user_id', '=', 'Technicians.user_id')
        ->selectRaw("COUNT(CASE WHEN availability_status = 'Available' THEN 1 END) as available")
        ->selectRaw("COUNT(CASE WHEN availability_status = 'Busy' THEN 1 END) as busy")
        ->first();

    $userCounts = [
        'total' => User::count(),
        'Students' => User::where('user_role', 'Student')->count(),
        'Staff_member' => User::where('user_role', 'Staff_Member')->count(),
        'technicians' => User::where('user_role', 'Technician')->count(),
        'admins' => User::where('user_role', 'Admin')->count(),
        'available_technicians' => $technicianStatus->available ?? 0,
        'busy_technicians' => $technicianStatus->busy ?? 0,
    ];

    // Task statistics
    $totalTasks = Task::count();
    $completedTasks = Task::where('issue_status', 'Completed')->count();
    $inProgressTasks = Task::where('issue_status', 'In Progress')->count();
    $pendingTasks = Task::where('issue_status', 'Pending')->count();

    $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

    $taskCounts = [
        'total' => $totalTasks,
        'completed' => $completedTasks,
        'in_progress' => $inProgressTasks,
        'pending' => $pendingTasks,
        'completion_rate' => $completionRate,
    ];

    // Priority distribution with percentages
    $priorityDistribution = [
        'high' => Task::where('priority', 'High')->count(),
        'medium' => Task::where('priority', 'Medium')->count(),
        'low' => Task::where('priority', 'Low')->count(),
        'high_percentage' => $totalTasks > 0 ? (Task::where('priority', 'High')->count() / $totalTasks) * 100 : 0,
        'medium_percentage' => $totalTasks > 0 ? (Task::where('priority', 'Medium')->count() / $totalTasks) * 100 : 0,
        'low_percentage' => $totalTasks > 0 ? (Task::where('priority', 'Low')->count() / $totalTasks) * 100 : 0,
    ];

    // Top performing technicians with completion percentages
    $topTechnicians = User::where('user_role', 'Technician')
        ->withCount(['tasks as completed_tasks' => function($query) {
            $query->where('issue_status', 'Completed');
        }])
        ->orderByDesc('completed_tasks')
        ->take(3)
        ->get()
        ->map(function($technician) use ($totalTasks) {
            $technician->completion_percentage = $totalTasks > 0 ? ($technician->completed_tasks / $totalTasks) * 100 : 0;
            return $technician;
        });

    return view('admin.dashboard', compact(
        'userCounts',
        'taskCounts',
        'priorityDistribution',
        'topTechnicians'
    ));
}

}
