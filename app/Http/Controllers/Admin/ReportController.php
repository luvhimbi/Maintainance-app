<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Task;
use App\Models\User;
use App\Models\Issue;
use App\Models\MaintenanceStaff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TechnicianPerformanceExport;
use App\Exports\TaskReportExport;
use App\Exports\TechnicianReportExport;
use App\Exports\PerformanceReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function technicianPerformance(Request $request)
    {
        $data = $this->getTechnicianReportData($request);
        return view('admin.reports.technician-performance', $data);
    }
    private function getTechnicianReportData(Request $request)
    {
        $statusFilter = $request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');
        $technicianId = $request->input('technician_id', 'all');

        $technicians = User::where('user_role', 'technician')
            ->withCount(['tasks as total_tasks' => function($query) use ($statusFilter, $priorityFilter) {
                $this->applyTaskFilters($query, $statusFilter, $priorityFilter);
            }])
            ->withCount(['tasks as completed_tasks' => function($query) use ($statusFilter, $priorityFilter) {
                $this->applyTaskFilters($query, $statusFilter, $priorityFilter)
                    ->where('issue_status', 'Completed');
            }])
            ->orderBy('first_name')
            ->get();

        return compact(
            'technicians',
            'statusFilter',
            'priorityFilter',
            'technicianId'
        );
    }

    private function applyTaskFilters($query, $status, $priority)
    {
        if ($status !== 'all') {
            $query->where('issue_status', $status);
        }
        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }
        return $query;
    }

    // PDF Export
    public function exportTechnicianPdf(Request $request)
    {
        $data = $this->getTechnicianReportData($request);
        $pdf = PDF::loadView('admin.reports.pdf.technician-performance', $data);
        return $pdf->download('technician-performance-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportPdf(Request $request)
    {
        $data = app(ReportController::class)->technicianPerformance($request)->getData();

        $pdf = PDF::loadView('admin.reports.technician-performance-pdf', [
            'technicians' => $data->technicians,
            'statusFilter' => $data->statusFilter,
            'priorityFilter' => $data->priorityFilter,
            'technicianId' => $data->technicianId,
        ]);

        return $pdf->download('technician-performance-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new TechnicianPerformanceExport($request), 'technician-performance.xlsx');
    }

    public function generateTaskReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $type = $request->input('type', 'pdf');

        $tasks = Task::with(['assignee', 'issue'])
            ->whereBetween('assignment_date', [$startDate, $endDate])
            ->get();

        $stats = [
            'total' => $tasks->count(),
            'completed' => $tasks->where('issue_status', 'completed')->count(),
            'pending' => $tasks->where('issue_status', 'pending')->count(),
            'in_progress' => $tasks->where('issue_status', 'in_progress')->count(),
            'high_priority' => $tasks->where('priority', 'high')->count(),
            'medium_priority' => $tasks->where('priority', 'medium')->count(),
            'low_priority' => $tasks->where('priority', 'low')->count(),
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
                $query->whereBetween('assignment_date', [$startDate, $endDate]);
            }, 'maintenanceStaff'])
            ->get()
            ->map(function($technician) {
                $completedTasks = $technician->tasks->where('issue_status', 'completed');
                $avgCompletionTime = $completedTasks->avg(function($task) {
                    return Carbon::parse($task->assignment_date)->diffInDays($task->expected_completion);
                });

                return [
                    'name' => $technician->first_name . ' ' . $technician->last_name,
                    'specialization' => $technician->maintenanceStaff->specialization ?? 'Not specified',
                    'availability' => $technician->maintenanceStaff->availability_status ?? 'Unknown',
                    'workload' => $technician->maintenanceStaff->current_workload ?? 0,
                    'total_tasks' => $technician->tasks->count(),
                    'completed_tasks' => $completedTasks->count(),
                    'completion_rate' => $technician->tasks->count() > 0 
                        ? round(($completedTasks->count() / $technician->tasks->count()) * 100, 2)
                        : 0,
                    'avg_completion_time' => $avgCompletionTime ?: 0
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
            'task_completion' => Task::whereBetween('assignment_date', [$startDate, $endDate])
                ->selectRaw("DATE(assignment_date) as date, COUNT(*) as total, 
                    SUM(CASE WHEN LOWER(issue_status) = 'completed' THEN 1 ELSE 0 END) as completed")
                ->groupBy('date')
                ->get(),
            'priority_distribution' => Task::whereBetween('assignment_date', [$startDate, $endDate])
                ->selectRaw('LOWER(priority) as priority, COUNT(*) as count')
                ->groupBy('priority')
                ->get(),
            'issue_types' => Issue::whereBetween('report_date', [$startDate, $endDate])
                ->selectRaw('issue_type, COUNT(*) as count')
                ->groupBy('issue_type')
                ->get(),
            'technician_performance' => User::where('user_role', 'Technician')
                ->with(['tasks' => function($query) use ($startDate, $endDate) {
                    $query->whereBetween('assignment_date', [$startDate, $endDate]);
                }, 'maintenanceStaff'])
                ->get()
                ->map(function($technician) {
                    $completedTasks = $technician->tasks->where('issue_status', 'completed');
                    return [
                        'name' => $technician->first_name . ' ' . $technician->last_name,
                        'specialization' => $technician->maintenanceStaff->specialization ?? 'Not specified',
                        'total_tasks' => $technician->tasks->count(),
                        'completed_tasks' => $completedTasks->count(),
                        'completion_rate' => $technician->tasks->count() > 0 
                            ? round(($completedTasks->count() / $technician->tasks->count()) * 100, 2)
                            : 0,
                        'current_workload' => $technician->maintenanceStaff->current_workload ?? 0
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