<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\MaintenanceStaff;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Excel;

class ReportController extends Controller
{





    public function generateTaskReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $type = $request->input('type', 'pdf');

        $tasks = Task::with(['assignee', 'issue'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $stats = [
            'total' => $tasks->count(),
            'completed' => $tasks->where('issue_status', 'Completed')->count(),
            'pending' => $tasks->where('issue_status', 'Pending')->count(),
            'in_progress' => $tasks->where('issue_status', 'In Progress')->count(),
            'high_priority' => $tasks->where('priority', 'High')->count(),
            'medium_priority' => $tasks->where('priority', 'Medium')->count(),
            'low_priority' => $tasks->where('priority', 'Low')->count(),
        ];

        $data = [
            'tasks' => $tasks,
            'stats' => $stats,
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y'),
        ];

        if ($type === 'pdf') {
            $pdf = PDF::loadView('admin.reports.task-pdf', $data);
            return $pdf->download('task-report.pdf');
        } else {
            return Excel::download(new TaskReportExport($data), 'task-report.xlsx');
        }
    }

    public function generateTechnicianReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $type = $request->input('type', 'pdf');

        $technicians = User::where('user_role', 'Technician')
            ->with(['tasks' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($technician) {
                return [
                    'name' => $technician->first_name . ' ' . $technician->last_name,
                    'total_tasks' => $technician->tasks->count(),
                    'completed_tasks' => $technician->tasks->where('issue_status', 'Completed')->count(),
                    'completion_rate' => $technician->tasks->count() > 0
                        ? round(($technician->tasks->where('issue_status', 'Completed')->count() / $technician->tasks->count()) * 100, 2)
                        : 0,
                    'avg_completion_time' => $technician->tasks->where('issue_status', 'Completed')
                        ->avg(function($task) {
                            return Carbon::parse($task->created_at)->diffInDays($task->expected_completion);
                        })
                ];
            });

        $data = [
            'technicians' => $technicians,
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y'),
        ];

        if ($type === 'pdf') {
            $pdf = PDF::loadView('admin.reports.technician-pdf', $data);
            return $pdf->download('technician-report.pdf');
        } else {
            return Excel::download(new TechnicianReportExport($data), 'technician-report.xlsx');
        }
    }

    public function generatePerformanceReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $type = $request->input('type', 'pdf');

        $performanceData = [
            'task_completion' => Task::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN issue_status = "Completed" THEN 1 ELSE 0 END) as completed')
                ->groupBy('date')
                ->get(),
            'priority_distribution' => Task::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->get(),
            'technician_performance' => User::where('user_role', 'Technician')
                ->with(['tasks' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->get()
                ->map(function($technician) {
                    return [
                        'name' => $technician->first_name . ' ' . $technician->last_name,
                        'total_tasks' => $technician->tasks->count(),
                        'completion_rate' => $technician->tasks->count() > 0
                            ? round(($technician->tasks->where('issue_status', 'Completed')->count() / $technician->tasks->count()) * 100, 2)
                            : 0
                    ];
                })
        ];

        $data = [
            'performance_data' => $performanceData,
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y'),
        ];

        if ($type === 'pdf') {
            $pdf = PDF::loadView('admin.reports.performance-pdf', $data);
            return $pdf->download('performance-report.pdf');
        } else {
            return Excel::download(new PerformanceReportExport($data), 'performance-report.xlsx');
        }
    }
}
