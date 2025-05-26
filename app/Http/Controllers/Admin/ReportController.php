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
    

    
    private function getTechnicianReportData(Request $request
        $statusFilter = $request$request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');
        $technicianId = $request->input('technician_id', 'all');

        $technicians = User::where('user_role', 'technician')
            ->when($technicianId !== 'all', function ($query) use ($technicianId) {
                $query->where('id', $technicianId);
            })
            ->withCount(['tasks as total_tasks' => function ($query) use ($statusFilter, $priorityFilter) {
                $this->applyTaskFilters($query, $statusFilter, $priorityFilter);
            }])
            ->withCount(['tasks as completed_tasks' => function ($query) use ($statusFilter, $priorityFilter) {
                $this->applyTaskFilters($query, $statusFilter, $priorityFilter)
                    ->where('issue_status', 'Completed');
            }])
            ->orderBy('first_name')
            ->get();

        return [
            'technicians' => $technicians,
            'statusFilter' => $statusFilter,
            'priorityFilter' => $priorityFilter,
            'technicianId' => $technicianId,
        ];
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
        $pdf = PDF::loadView('admin.reports.technician', $data);
        return $pdf->download('technician-performance'.now()->format('Y-m-d').'.pdf');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getTechnicianReportData($request);

        $pdf = PDF::loadView('admin.reports.technician-pdf', $data);

        return $pdf->download('technician-performance.pdf');
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
        
        // Get filter values
        $statusFilter = $request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');
        $technicianFilter = $request->input('technician_id', 'all');
        $issueTypeFilter = $request->input('issue_type', 'all');
    
        $tasks = Task::with(['assignee', 'issue'])
            ->whereBetween('assignment_date', [$startDate, $endDate])
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('issue_status', $statusFilter);
            })
            ->when($priorityFilter !== 'all', function ($query) use ($priorityFilter) {
                $query->where('priority', $priorityFilter);
            })
            ->when($technicianFilter !== 'all', function ($query) use ($technicianFilter) {
                $query->where('assigned_to', $technicianFilter);
            })
            ->when($issueTypeFilter !== 'all', function ($query) use ($issueTypeFilter) {
                $query->whereHas('issue', function($q) use ($issueTypeFilter) {
                    $q->where('issue_type', $issueTypeFilter);
                });
            })
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
            'filters' => [
                'status' => $statusFilter,
                'priority' => $priorityFilter,
                'technician_id' => $technicianFilter,
                'issue_type' => $issueTypeFilter,
            ],
            'technicians' => User::where('user_role', 'Technician')->get(),
            'issue_types' => Issue::select('issue_type')->distinct()->pluck('issue_type'),
        ];
    
        if ($type === 'pdf') {
            $pdf = PDF::loadView('admin.reports.tasks', $data);
            return $pdf->download('task-report-'.now()->format('Y-m-d').'.pdf');
        } elseif ($type === 'excel') {
            return Excel::download(new TaskReportExport($data), 'task-report-'.now()->format('Y-m-d').'.xlsx');
        }
    
        return view('admin.reports.task-summary', $data);
    }
    
    public function generateTechnicianReport(Request $request)
{
    $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
    $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

    // Get filter values
    $statusFilter = $request->input('status', 'all');
    $priorityFilter = $request->input('priority', 'all');

    $technicians = User::where('user_role', 'Technician')
        ->whereHas('tasks', function ($query) use ($startDate, $endDate, $statusFilter, $priorityFilter) {
            $query->whereBetween('assignment_date', [$startDate, $endDate])
                ->when($statusFilter !== 'all', function ($q) use ($statusFilter) {
                    $q->where('issue_status', $statusFilter);
                })
                ->when($priorityFilter !== 'all', function ($q) use ($priorityFilter) {
                    $q->where('priority', $priorityFilter);
                });
        })
        ->with(['tasks' => function ($query) use ($startDate, $endDate, $statusFilter, $priorityFilter) {
            $query->whereBetween('assignment_date', [$startDate, $endDate])
                ->when($statusFilter !== 'all', function ($q) use ($statusFilter) {
                    $q->where('issue_status', $statusFilter);
                })
                ->when($priorityFilter !== 'all', function ($q) use ($priorityFilter) {
                    $q->where('priority', $priorityFilter);
                });
        }, 'maintenanceStaff'])
        ->get()
        ->map(function ($technician) {
            $completedTasks = $technician->tasks->where('issue_status', 'Completed');
            $avgCompletionTime = $completedTasks->avg(function ($task) {
                return Carbon::parse($task->assignment_date)->diffInDays($task->expected_completion);
            });

            return [
                'id' => $technician->id,
                'name' => $technician->first_name . ' ' . $technician->last_name,
                'specialization' => $technician->maintenanceStaff->specialization ?? 'Not specified',
                'availability' => $technician->maintenanceStaff->availability_status ?? 'Unknown',
                'workload' => $technician->maintenanceStaff->current_workload ?? 0,
                'total_tasks' => $technician->tasks->count(),
                'completed_tasks' => $completedTasks->count(),
                'completion_rate' => $technician->tasks->count() > 0
                    ? round(($completedTasks->count() / $technician->tasks->count()) * 100, 2)
                    : 0,
                'avg_completion_time' => $avgCompletionTime ? round($avgCompletionTime, 1) : 0,
            ];
        });

    $data = [
        'technicians' => $technicians,
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
        'filters' => [
            'status' => $statusFilter,
            'priority' => $priorityFilter,
        ],
    ];

    return view('admin.reports.technician', compact('technicians', 'startDate', 'endDate', 'filters'));
}



    //THIS METHODS DISPLAY THE SUMMARY INFORMATION FOR THE TASKS AND TECHNICIANS
    public function taskReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $statusFilter = $request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');

        $tasks = Task::with(['assignee', 'issue'])
            ->whereBetween('assignment_date', [$startDate, $endDate])
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('issue_status', $statusFilter);
            })
            ->when($priorityFilter !== 'all', function ($query) use ($priorityFilter) {
                $query->where('priority', $priorityFilter);
            })
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

        $filters = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => $statusFilter,
            'priority' => $priorityFilter,
        ];

        return view('admin.reports.tasks', compact('tasks', 'stats', 'filters', 'startDate', 'endDate'));
    }

    public function technicianReport(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $statusFilter = $request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');
        $technicianId = $request->input('technician_id', 'all');
    
        $technicians = User::where('user_role', 'Technician')
            ->when($technicianId !== 'all', function ($query) use ($technicianId) {
                $query->where('id', $technicianId);
            })
            ->with(['maintenanceStaff', 'tasks' => function($query) use ($startDate, $endDate, $statusFilter, $priorityFilter) {
                $query->whereBetween('assignment_date', [$startDate, $endDate]);
                $this->applyTaskFilters($query, $statusFilter, $priorityFilter);
            }])
            ->orderBy('first_name')
            ->get()
            ->map(function($technician) {
                $completedTasks = $technician->tasks->where('issue_status', 'Completed');
                $avgCompletionTime = $completedTasks->avg(function($task) {
                    return Carbon::parse($task->assignment_date)->diffInDays($task->expected_completion);
                });
    
                return [
                    'id' => $technician->id,
                    'first_name' => $technician->first_name,
                    'last_name' => $technician->last_name,
                    'specialization' => $technician->maintenanceStaff->specialization ?? 'Not specified',
                    'availability_status' => $technician->maintenanceStaff->availability_status ?? 'Unknown',
                    'current_workload' => $technician->maintenanceStaff->current_workload ?? 0,
                    'total_tasks' => $technician->tasks->count(),
                    'completed_tasks' => $completedTasks->count(),
                    'completion_rate' => $technician->tasks->count() > 0 
                        ? round(($completedTasks->count() / $technician->tasks->count()) * 100, 2)
                        : 0,
                    'avg_completion_time' => $avgCompletionTime ? round($avgCompletionTime, 2) : 0
                ];
            });
    
        // Define summary statistics
        $stats = [
            'total_technicians' => $technicians->count(),
            'total_tasks' => $technicians->sum('total_tasks'),
            'completed_tasks' => $technicians->sum('completed_tasks'),
            'avg_completion_rate' => $technicians->avg('completion_rate') ?? 0,
            'avg_completion_time' => $technicians->avg('avg_completion_time') ?? 0,
        ];
    
        $filters = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => $statusFilter,
            'priority' => $priorityFilter,
            'technician_id' => $technicianId,
        ];
    
        return view('admin.reports.technician', compact('technicians', 'filters', 'stats', 'startDate', 'endDate'));
    } 



    public function exportTechnicianExcel(Request $request)
    {
        $data = $this->prepareTechnicianReportData($request);
        return Excel::download(new TechnicianReportExport($data), 'technician-report-' . now()->format('Y-m-d') . '.xlsx');
    }

 
    public function exportTaskPdf(Request $request)
    {
        $data = $this->prepareTaskReportData($request);
        $pdf = PDF::loadView('admin.reports.task-pdf', $data);
        return $pdf->download('task-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportTaskExcel(Request $request)
    {
        $data = $this->prepareTaskReportData($request);
        return Excel::download(new TaskReportExport($data), 'task-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    private function prepareTaskReportData(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $statusFilter = $request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');

        $tasks = Task::with(['assignee', 'issue'])
            ->whereBetween('assignment_date', [$startDate, $endDate])
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('issue_status', $statusFilter);
            })
            ->when($priorityFilter !== 'all', function ($query) use ($priorityFilter) {
                $query->where('priority', $priorityFilter);
            })
            ->get();

        $stats = [
            'total' => $tasks->count(),
            'completed' => $tasks->where('issue_status', 'completed')->count(),
            'pending' => $tasks->where('issue_status', 'pending')->count(),
            'in_progress' => $tasks->where('issue_status', 'In Progress')->count(),
            'high_priority' => $tasks->where('priority', 'high')->count(),
            'medium_priority' => $tasks->where('priority', 'medium')->count(),
            'low_priority' => $tasks->where('priority', 'low')->count(),
        ];

        return compact('tasks', 'stats', 'startDate', 'endDate');
    }

    private function prepareTechnicianReportData(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        $statusFilter = $request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');

        $technicians = User::where('user_role', 'Technician')
            ->with(['maintenanceStaff', 'tasks' => function ($query) use ($startDate, $endDate, $statusFilter, $priorityFilter) {
                $query->whereBetween('assignment_date', [$startDate, $endDate]);
                $this->applyTaskFilters($query, $statusFilter, $priorityFilter);
            }])
            ->get()
            ->map(function ($technician) {
                $completedTasks = $technician->tasks->where('issue_status', 'completed');
                $avgCompletionTime = $completedTasks->avg(function ($task) {
                    return Carbon::parse($task->assignment_date)->diffInDays($task->expected_completion);
                });

                return [
                    'user_id' => $technician->id,
                    'first_name' => $technician->first_name,
                    'last_name' => $technician->last_name,
                    'specialization' => $technician->maintenanceStaff->specialization ?? 'Not specified',
                    'availability_status' => $technician->maintenanceStaff->availability_status ?? 'Unknown',
                    'current_workload' => $technician->maintenanceStaff->current_workload ?? 0,
                    'total_tasks' => $technician->tasks->count(),
                    'completed_tasks' => $completedTasks->count(),
                    'completion_rate' => $technician->tasks->count() > 0
                        ? round(($completedTasks->count() / $technician->tasks->count()) * 100, 2)
                        : 0,
                    'avg_completion_time' => $avgCompletionTime ? round($avgCompletionTime, 2) : 0,
                ];
            });

        return compact('technicians', 'startDate', 'endDate');
    }

  
   

   

      
}