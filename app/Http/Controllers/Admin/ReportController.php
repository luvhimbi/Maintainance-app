<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TechnicianPerformanceExport;        
use PDF;

class ReportController extends Controller
{

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
            ->orderBy('username')
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
}