<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function technicianPerformance(Request $request)
    {
        // Get filter values from request or set defaults
        $statusFilter = $request->input('status', 'all');
        $priorityFilter = $request->input('priority', 'all');
        $technicianId = $request->input('technician_id', 'all');
        
        // Base query for technicians
        $technicians = User::where('user_role', 'technician')
            ->orderBy('username')
            ->get();

        // Performance metrics calculation
        $technicians->each(function ($tech) use ($statusFilter, $priorityFilter) {
            $query = $tech->tasks();
            
            // Apply status filter
            if ($statusFilter !== 'all') {
                $query->where('issue_status', $statusFilter);
            }
            
            // Apply priority filter
            if ($priorityFilter !== 'all') {
                $query->where('priority', $priorityFilter);
            }
            
            $tasks = $query->get();
            
            // Metrics calculation
            $tech->total_tasks = $tasks->count();
            $tech->completed_tasks = $tasks->where('issue_status', 'Completed')->count();
            $tech->pending_tasks = $tasks->where('issue_status', 'Pending')->count();
            $tech->in_progress_tasks = $tasks->where('issue_status', 'In Progress')->count();
            $tech->high_priority_tasks = $tasks->where('priority', 'High')->count();
            
            // Performance score (custom formula)
            $tech->performance_score = $tech->total_tasks > 0 
                ? round(
                    ($tech->completed_tasks * 0.6) - 
                    ($tech->pending_tasks * 0.2) - 
                    ($tech->high_priority_tasks * 0.2) - 
                    ($tech->total_tasks * 100), 1
                )
                : 0;
        });

        // Filter for specific technician if selected
        if ($technicianId !== 'all') {
            $technicians = $technicians->where('id', $technicianId);
        }

        return view('admin.reports.technician-performance', compact(
            'technicians',
            'statusFilter',
            'priorityFilter',
            'technicianId'
        ));
    }
}