<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TaskExport;
use App\Exports\StudentsAndStaffExport;
use App\Exports\TechnicianReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ReportController extends Controller
{
    public function StudentsAndStaffReport(Request $request)
    {
        // 1. Validate Inputs
        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'search' => 'nullable|string|max:255',
        ]);

        $startDate = $validatedData['start_date'] ?? null;
        $endDate = $validatedData['end_date'] ?? null;
        $searchTerm = $validatedData['search'] ?? null;

        // Base query for users who are Students and have reported issues
        $studentQuery = User::where('user_role', 'Student')
            ->has('issues') // Only users who have reported at least one issue
            ->with(['studentDetail', 'issues' => function ($query) use ($startDate, $endDate) {
                // Conditionally filter eager loaded issues by date range
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }]);

        // Base query for users who are Staff_Members and have reported issues
        $staffMembersQuery = User::where('user_role', 'Staff_Member')
            ->has('issues') // Only users who have reported at least one issue
            ->with(['staffDetail', 'issues' => function ($query) use ($startDate, $endDate) {
                // Conditionally filter eager loaded issues by date range
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }]);

        // Apply date range filter to the primary user query if dates are provided
        if ($startDate && $endDate) {
            $studentQuery->whereHas('issues', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

            $staffMembersQuery->whereHas('issues', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        // Apply search filter if a search term is provided
        if ($searchTerm) {
            $studentQuery->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('studentDetail', function ($q) use ($searchTerm) {
                        $q->where('student_number', 'ILIKE', '%' . $searchTerm . '%')
                            ->orWhere('course', 'ILIKE', '%' . $searchTerm . '%');
                    });
            });

            $staffMembersQuery->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('staffDetail', function ($q) use ($searchTerm) {
                        $q->where('department', 'ILIKE', '%' . $searchTerm . '%')
                            ->orWhere('position_title', 'ILIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        $students = $studentQuery->get();
        $staffMembers = $staffMembersQuery->get();

        // Calculate total issues reported by students within the applied filters
        $totalStudentIssues = $students->sum(function($student) {
            return $student->issues->count(); // This will count the *filtered* issues
        });

        // Calculate total issues reported by staff members within the applied filters
        $totalStaffIssues = $staffMembers->sum(function($staff) {
            return $staff->issues->count(); // This will count the *filtered* issues
        });

        return view('admin.reports.students_and_staff_report', compact('students', 'staffMembers', 'totalStudentIssues', 'totalStaffIssues', 'startDate', 'endDate', 'searchTerm'));
    }


// this is for getting the data which is filtered
    private function getFilteredReportData(Request $request)
    {
        // 1. Validate Inputs (re-use validation logic)
        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'search' => 'nullable|string|max:255',
        ]);

        $startDate = $validatedData['start_date'] ?? null;
        $endDate = $validatedData['end_date'] ?? null;
        $searchTerm = $validatedData['search'] ?? null;

        // Base query for users who are Students and have reported issues
        $studentQuery = User::where('user_role', 'Student')
            ->has('issues')
            ->with(['studentDetail', 'issues' => function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }]);

        // Base query for users who are Staff_Members and have reported issues
        $staffMembersQuery = User::where('user_role', 'Staff_Member')
            ->has('issues')
            ->with(['staffDetail', 'issues' => function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }]);

        if ($startDate && $endDate) {
            $studentQuery->whereHas('issues', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });

            $staffMembersQuery->whereHas('issues', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        if ($searchTerm) {
            $studentQuery->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('studentDetail', function ($q) use ($searchTerm) {
                        $q->where('student_number', 'ILIKE', '%' . $searchTerm . '%')
                            ->orWhere('course', 'ILIKE', '%' . $searchTerm . '%');
                    });
            });

            $staffMembersQuery->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'ILIKE', '%' . $searchTerm . '%')
                    ->orWhereHas('staffDetail', function ($q) use ($searchTerm) {
                        $q->where('department', 'ILIKE', '%' . $searchTerm . '%')
                            ->orWhere('position_title', 'ILIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        $students = $studentQuery->get();
        $staffMembers = $staffMembersQuery->get();

        $totalStudentIssues = $students->sum(function($student) {
            return $student->issues->count();
        });

        $totalStaffIssues = $staffMembers->sum(function($staff) {
            return $staff->issues->count();
        });

        return compact('students', 'staffMembers', 'totalStudentIssues', 'totalStaffIssues', 'startDate', 'endDate', 'searchTerm');
    }


    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredReportData($request);

        try {
            $pdf = Pdf::loadView('admin.reports.students_and_staff_pdf', $data);
            return $pdf->download('students_and_staff_report.pdf');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF report. Please ensure Dompdf is installed and configured correctly.');
        }
    }
    public function exportExcel(Request $request)
    {
        // Use the helper method to get the filtered data
        $data = $this->getFilteredReportData($request);

        // Pass the entire data array to the export class
        return Excel::download(new StudentsAndStaffExport($data), 'students_and_staff_report.xlsx');
    }
    public function exportWord(Request $request)
    {
        // 1. Get Filtered Data
        $data = $this->getFilteredReportData($request);
        $students = $data['students'];
        $staffMembers = $data['staffMembers'];
        $totalStudentIssues = $data['totalStudentIssues'];
        $totalStaffIssues = $data['totalStaffIssues'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $searchTerm = $data['searchTerm'];

        // 2. Create a new PhpWord object with proper page setup
        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'marginTop' => 600,
            'marginBottom' => 600,
            'marginLeft' => 800,
            'marginRight' => 800,
        ]);

        // 3. Define styles
        $fontStyleTitle = ['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => '2C3E50'];
        $fontStyleSub = ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => '34495E'];
        $fontStyleNormal = ['name' => 'Arial', 'size' => 10, 'color' => '333333'];
        $fontStyleSmall = ['name' => 'Arial', 'size' => 9, 'color' => '555555'];
        $paraStyleCenter = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
        $paraStyleLeft = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT];

        // 4. Add General Report Information Section
        $section->addText('OCM - Online Campus Management', $fontStyleSmall, $paraStyleCenter);
        $section->addText('Students & Staff Report', $fontStyleTitle, $paraStyleCenter);
        $section->addText('Report Generated: ' . now()->format('F j, Y \a\t H:i A T'), $fontStyleSmall, $paraStyleCenter);
        $section->addTextBreak(1);

        // 5. Add Report Parameters
        $section->addText('Report Parameters:', $fontStyleSub, $paraStyleLeft);
        if ($startDate && $endDate) {
            $section->addText('Date Range: ' . \Carbon\Carbon::parse($startDate)->format('F j, Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('F j, Y'), $fontStyleNormal);
        }
        if ($searchTerm) {
            $section->addText('Search Term: "' . $searchTerm . '"', $fontStyleNormal);
        }
        $section->addText('Total Students (filtered): ' . $students->count(), $fontStyleNormal);
        $section->addText('Total Staff (filtered): ' . $staffMembers->count(), $fontStyleNormal);
        $section->addText('Total Issues Reported (overall): ' . ($totalStudentIssues + $totalStaffIssues), $fontStyleNormal);
        $section->addTextBreak(1);

        // 6. Define table styles
        $tableStyle = [
            'borderColor' => 'D0D0D0',
            'borderSize'  => 6,
            'cellMargin'  => 80,
            'alignment'   => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'width'       => 100 * 50,
            'unit'        => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT,
        ];
        $headerCellStyle = ['bgColor' => 'F5F5F5', 'valign' => 'center'];
        $headerFont = ['bold' => true, 'size' => 10, 'color' => '555555'];

        // 7. Add Students Section
        $section->addText('Students Overview', $fontStyleSub, $paraStyleLeft);
        $section->addTextBreak(1);

        if ($students->isNotEmpty()) {
            $phpWord->addTableStyle('StudentTable', $tableStyle, $headerCellStyle);
            $table = $section->addTable('StudentTable');
            
            // Add header row
            $table->addRow(400);
            $table->addCell(2000, $headerCellStyle)->addText('Name', $headerFont, $paraStyleCenter);
            $table->addCell(2500, $headerCellStyle)->addText('Email', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Student No.', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Course', $headerFont, $paraStyleCenter);
            $table->addCell(1000, $headerCellStyle)->addText('Issues', $headerFont, $paraStyleCenter);

            // Add data rows
            foreach ($students as $student) {
                $table->addRow();
                $table->addCell(2000)->addText($student->first_name . ' ' . $student->last_name, null, $paraStyleLeft);
                $table->addCell(2500)->addText($student->email, null, $paraStyleLeft);
                $table->addCell(1500)->addText($student->studentDetail->student_number ?? 'N/A', null, $paraStyleLeft);
                $table->addCell(1500)->addText($student->studentDetail->course ?? 'N/A', null, $paraStyleLeft);
                $table->addCell(1000)->addText($student->issues->count(), null, $paraStyleCenter);
            }
        } else {
            $section->addText('No student accounts found matching your criteria.', $fontStyleNormal);
        }

        $section->addTextBreak(2);

        // 8. Add Staff Members Section
        $section->addText('Staff Members Overview', $fontStyleSub, $paraStyleLeft);
        $section->addTextBreak(1);

        if ($staffMembers->isNotEmpty()) {
            $phpWord->addTableStyle('StaffTable', $tableStyle, $headerCellStyle);
            $table = $section->addTable('StaffTable');
            
            // Add header row
            $table->addRow(400);
            $table->addCell(2000, $headerCellStyle)->addText('Name', $headerFont, $paraStyleCenter);
            $table->addCell(2500, $headerCellStyle)->addText('Email', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Department', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Position', $headerFont, $paraStyleCenter);
            $table->addCell(1000, $headerCellStyle)->addText('Issues', $headerFont, $paraStyleCenter);

            // Add data rows
            foreach ($staffMembers as $staff) {
                $table->addRow();
                $table->addCell(2000)->addText($staff->first_name . ' ' . $staff->last_name, null, $paraStyleLeft);
                $table->addCell(2500)->addText($staff->email, null, $paraStyleLeft);
                $table->addCell(1500)->addText($staff->staffDetail->department ?? 'N/A', null, $paraStyleLeft);
                $table->addCell(1500)->addText($staff->staffDetail->position_title ?? 'N/A', null, $paraStyleLeft);
                $table->addCell(1000)->addText($staff->issues->count(), null, $paraStyleCenter);
            }
        } else {
            $section->addText('No staff member accounts found matching your criteria.', $fontStyleNormal);
        }

        // 9. Save and download the document
        try {
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $fileName = 'students_and_staff_report_' . now()->format('Ymd_His') . '.docx';

            // Create a temporary file
            $tempFile = storage_path('app/public/' . $fileName);
            $objWriter->save($tempFile);

            // Return the file as a download
            return response()->download($tempFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Word export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Word report. Please try again or contact support if the issue persists.');
        }
    }






    public function MaintenanceTaskReport(Request $request)
    {
        // Get paginated data for display
        $data = $this->getFilteredTaskReportData($request, true); // true for pagination

        // Extract data for the view
        $tasks = $data['tasks'];
        $stats = $data['stats'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $filters = $data['filters'];

        return view('admin.Reports.tasks', compact('tasks', 'stats', 'startDate', 'endDate', 'filters'));
    }

    /**
     * Helper method to get filtered task report data.
     *
     * @param Request $request
     * @param bool $paginate Whether to paginate the results (true for display, false for export)
     * @return array
     */
    private function getFilteredTaskReportData(Request $request, bool $paginate = false): array
    {
        $query = Task::with([
            'issue' => function($q) {
                $q->with(['location' => function($q) {
                    $q->with(['building', 'floor', 'room']);
                }]);
            },
            'assignee'
        ]);

        // Apply date filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('issue_status', $request->status);
        }

        // Apply priority filter
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Get all tasks for stats calculation (without pagination)
        $allFilteredTasks = $query->get();

        // Calculate statistics
        $stats = [
            'total' => $allFilteredTasks->count(),
            'completed' => $allFilteredTasks->where('issue_status', 'Completed')->count(),
            'pending' => $allFilteredTasks->where('issue_status', 'Pending')->count(),
            'in_progress' => $allFilteredTasks->where('issue_status', 'In Progress')->count(),
            'overdue' => $allFilteredTasks->filter(function ($task) {
                return $task->expected_completion < now() && $task->issue_status != 'Completed';
            })->count(),
        ];

        // Apply pagination if requested
        $tasks = $paginate ? $query->paginate(10) : $query->get();

        // Set default dates if no tasks found
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return [
            'tasks' => $tasks,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'priority' => $request->priority,
            ],
        ];
    }


    //THIS METHODS DISPLAY THE SUMMARY INFORMATION FOR THE TASKS AND TECHNICIANS





    public function exportTaskExcel(Request $request)
    {
        $data = $this->getFilteredTaskReportData($request, false); // false for no pagination

        try {
            return Excel::download(new TaskExport($data), 'maintenance_task_report_' . now()->format('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Task Excel export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Excel report. Please ensure Maatwebsite/Excel is installed and configured correctly.');
        }
    }


    public function exportTaskPdf(Request $request)
    {
        $data = $this->getFilteredTaskReportData($request, false); // false for no pagination
        $data['reportTitle'] = 'Maintenance Task Report'; // Add title for PDF view

        try {
            $pdf = Pdf::loadView('admin.reports.task_report_pdf', $data);
            return $pdf->download('maintenance_task_report_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Task PDF export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF report. Please ensure Dompdf is installed and configured correctly.');
        }
    }

    public function exportTaskWord(Request $request)
    {
        $data = $this->getFilteredTaskReportData($request, false); // false for no pagination
        $tasks = $data['tasks'];
        $stats = $data['stats'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $filters = $data['filters'];

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection([
            'marginTop' => 600,
            'marginBottom' => 600,
            'marginLeft' => 800,
            'marginRight' => 800,
        ]);

        // Styles
        $fontStyleTitle = ['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => '2C3E50'];
        $fontStyleSub = ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => '34495E'];
        $fontStyleNormal = ['name' => 'Arial', 'size' => 10, 'color' => '333333'];
        $fontStyleSmall = ['name' => 'Arial', 'size' => 9, 'color' => '555555'];
        $paraStyleCenter = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
        $paraStyleLeft = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT];

        $tableStyle = [
            'borderColor' => 'D0D0D0',
            'borderSize'  => 6,
            'cellMargin'  => 80,
            'alignment'   => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'width'       => 100 * 50, // 100% in twips (1% = 50 twips)
            'unit'        => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT,
        ];
        $headerCellStyle = ['bgColor' => 'F5F5F5', 'valign' => 'center'];
        $headerFont = ['bold' => true, 'size' => 10, 'color' => '555555'];

        // Header
        $section->addText('OCM - Online Campus Management', $fontStyleSmall, $paraStyleCenter);
        $section->addText('Maintenance Task Report', $fontStyleTitle, $paraStyleCenter);
        $section->addText('Generated: ' . now()->format('F j, Y \a\t H:i A T'), $fontStyleSmall, $paraStyleCenter);
        $section->addTextBreak(1);

        // Summary
        $section->addText('Report Summary:', $fontStyleSub, $paraStyleLeft);
        $section->addText('Period: ' . $startDate->format('F j, Y') . ' to ' . $endDate->format('F j, Y'), $fontStyleNormal);
        $section->addText('Total Tasks: ' . $stats['total'], $fontStyleNormal);
        $section->addText('Completed Tasks: ' . $stats['completed'], $fontStyleNormal);
        $section->addText('Pending Tasks: ' . $stats['pending'], $fontStyleNormal);
        $section->addText('In Progress Tasks: ' . $stats['in_progress'], $fontStyleNormal);
        $section->addText('Overdue Tasks: ' . $stats['overdue'], $fontStyleNormal);
        $section->addTextBreak(1);

        // Filters
        $section->addText('Filters Applied:', $fontStyleSub, $paraStyleLeft);
        $section->addText('Status: ' . ucfirst(str_replace('_', ' ', $filters['status'] ?? 'All')), $fontStyleNormal);
        $section->addText('Priority: ' . ucfirst($filters['priority'] ?? 'All'), $fontStyleNormal);
        $section->addTextBreak(2);

        // Task Details Table
        $section->addText('Task Details:', $fontStyleSub, $paraStyleLeft);
        $section->addTextBreak(1);

        if ($tasks->isNotEmpty()) {
            $phpWord->addTableStyle('TaskTable', $tableStyle, $headerCellStyle);
            $table = $section->addTable('TaskTable');
            $table->addRow(400);

            // Table Headers
            $table->addCell(1200, $headerCellStyle)->addText('Task ID', $headerFont, $paraStyleCenter);
            $table->addCell(1600, $headerCellStyle)->addText('Issue Type', $headerFont, $paraStyleCenter);
            $table->addCell(1800, $headerCellStyle)->addText('Location', $headerFont, $paraStyleCenter);
            $table->addCell(1800, $headerCellStyle)->addText('Assignee', $headerFont, $paraStyleCenter);
            $table->addCell(1200, $headerCellStyle)->addText('Status', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Due Date', $headerFont, $paraStyleCenter);

            // Table Rows
            foreach ($tasks as $task) {
                $table->addRow();

                $table->addCell(1200)->addText('#' . $task->task_id, null, $paraStyleLeft);

                $table->addCell(1600)->addText(
                    $task->issue->issue_type ?? 'N/A',
                    null,
                    $paraStyleLeft
                );

                $location = ($task->issue->location->building_name ?? 'N/A');
                if (!empty($task->issue->location->room_number)) {
                    $location .= ' (Room ' . $task->issue->location->room_number . ')';
                }
                $table->addCell(1800)->addText($location, null, $paraStyleLeft);

                $assignee = $task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned';
                $table->addCell(1800)->addText($assignee, null, $paraStyleLeft);

                $statusText = ucfirst(str_replace('_', ' ', $task->issue_status));
                $statusColor = '000000';
                if ($task->issue_status === 'completed') {
                    $statusColor = '28A745';
                } elseif ($task->issue_status === 'pending') {
                    $statusColor = 'FFC107';
                } elseif ($task->issue_status === 'in_progress') {
                    $statusColor = '0D6EFD';
                } elseif ($task->expected_completion < now() && $task->issue_status !== 'completed') {
                    $statusColor = 'DC3545';
                }
                $table->addCell(1200)->addText($statusText, ['color' => $statusColor, 'bold' => true], $paraStyleCenter);

                $dueDateText = $task->expected_completion->format('M d, Y');
                $dueDateColor = ($task->expected_completion < now() && $task->issue_status !== 'completed') ? 'DC3545' : '000000';
                $table->addCell(1500)->addText($dueDateText, ['color' => $dueDateColor, 'bold' => true], $paraStyleCenter);
            }
        } else {
            $section->addText('No tasks found matching your criteria.', $fontStyleNormal);
        }

        // Save the file and return download
        $fileName = 'task_report_' . now()->format('Ymd_His') . '.docx';
        $tempFile = storage_path('app/public/' . $fileName);
        $phpWord->save($tempFile, 'Word2007');

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }





    /**
     * Display the Technician Report.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function generateTechnicianReport(Request $request)
    {
        $data = $this->getFilteredTechnicianReportData($request);

        $technicians = $data['technicians'];
        $stats = $data['stats'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $filters = $data['filters'];

        return view('admin.reports.technician', compact('technicians', 'stats', 'startDate', 'endDate', 'filters'));
    }

    /**
     * Helper method to get filtered technician report data.
     *
     * @param Request $request
     * @return array
     */
    private function getFilteredTechnicianReportData(Request $request): array
    {
        // 1. Validate Inputs
        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => ['nullable', 'string', Rule::in(['all', 'completed', 'pending', 'in_progress'])],
            'priority' => ['nullable', 'string', Rule::in(['all', 'high', 'medium', 'low'])],
        ]);

        // Ensure start_date and end_date are Carbon instances, with defaults if not provided
        $startDateInput = $validatedData['start_date'] ?? null;
        $endDateInput = $validatedData['end_date'] ?? null;

        $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : Carbon::today()->subMonth()->startOfDay();
        $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::today()->endOfDay();

        $statusFilter = $validatedData['status'] ?? 'all';
        $priorityFilter = $validatedData['priority'] ?? 'all';

        $technicians = User::where('user_role', 'Technician')
            ->whereHas('tasks', function($query) use ($startDate, $endDate, $statusFilter, $priorityFilter) {
                $query->whereBetween('assignment_date', [$startDate, $endDate])
                    ->when($statusFilter !== 'all', function ($q) use ($statusFilter) {
                        $status = match($statusFilter) {
                            'completed' => 'Completed',
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            default => $statusFilter
                        };
                        $q->where('issue_status', $status);
                    })
                    ->when($priorityFilter !== 'all', function ($q) use ($priorityFilter) {
                        $priority = match($priorityFilter) {
                            'high' => 'High',
                            'medium' => 'Medium',
                            'low' => 'Low',
                            default => $priorityFilter
                        };
                        $q->where('priority', $priority);
                    });
            })
            ->with(['maintenanceStaff', 'tasks' => function ($query) use ($startDate, $endDate, $statusFilter, $priorityFilter) {
                $query->whereBetween('assignment_date', [$startDate, $endDate])
                    ->when($statusFilter !== 'all', function ($q) use ($statusFilter) {
                        $status = match($statusFilter) {
                            'completed' => 'Completed',
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            default => $statusFilter
                        };
                        $q->where('issue_status', $status);
                    })
                    ->when($priorityFilter !== 'all', function ($q) use ($priorityFilter) {
                        $priority = match($priorityFilter) {
                            'high' => 'High',
                            'medium' => 'Medium',
                            'low' => 'Low',
                            default => $priorityFilter
                        };
                        $q->where('priority', $priority);
                    });
            }])
            ->get()
            ->map(function ($technician) use ($startDate, $endDate) {
                $filteredTasks = $technician->tasks->filter(function($task) use ($startDate, $endDate) {
                    return $task->assignment_date->between($startDate, $endDate);
                });

                $completedTasks = $filteredTasks->where('issue_status', 'Completed');
                $totalTasks = $filteredTasks->count();

                $avgCompletionTime = null;
                if ($completedTasks->isNotEmpty()) {
                    $totalCompletionTimeInDays = 0;
                    foreach ($completedTasks as $task) {
                        $completionDate = $task->actual_completion_date ?? $task->expected_completion ?? Carbon::now();
                        $totalCompletionTimeInDays += Carbon::parse($task->assignment_date)->diffInDays($completionDate);
                    }
                    $avgCompletionTime = $totalCompletionTimeInDays / $completedTasks->count();
                }

                return [
                    'id' => $technician->id,
                    'name' => $technician->first_name . ' ' . $technician->last_name,
                    'email' => $technician->email,
                    'phone_number' => $technician->phone_number,
                    'address' => $technician->address,
                    'specialization' => $technician->maintenanceStaff->specialization ?? 'N/A',
                    'availability' => $technician->maintenanceStaff->availability_status ?? 'Unknown',
                    'workload' => $technician->maintenanceStaff->current_workload ?? 0,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks->count(),
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks->count() / $totalTasks) * 100, 2) : 0,
                    'avg_completion_time' => $avgCompletionTime ? round($avgCompletionTime, 1) : 0
                ];
            })
            ->filter(function($technician) {
                return $technician['total_tasks'] > 0;
            });

        // Calculate overall statistics
        $stats = [
            'total_technicians' => $technicians->count(),
            'total_tasks' => $technicians->sum('total_tasks'),
            'completed_tasks' => $technicians->sum('completed_tasks'),
            'avg_completion_rate' => $technicians->avg('completion_rate'),
            'avg_completion_time' => $technicians->filter(function($t) { return $t['avg_completion_time'] !== null; })->avg('avg_completion_time')
        ];

        return [
            'technicians' => $technicians,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filters' => [
                'start_date' => $startDateInput,
                'end_date' => $endDateInput,
                'status' => $statusFilter,
                'priority' => $priorityFilter
            ]
        ];
    }

    /**
     * Export the Technician Report to PDF.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportTechnicianPdf(Request $request)
    {
        $data = $this->getFilteredTechnicianReportData($request);
        $data['reportTitle'] = 'Technician Performance Report';

        try {
            $pdf = Pdf::loadView('admin.reports.technician_report_pdf', $data);
            return $pdf->download('technician_report_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Technician PDF export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF report. ' . $e->getMessage());
        }
    }

    /**
     * Export the Technician Report to Excel.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportTechnicianExcel(Request $request)
    {
        $data = $this->getFilteredTechnicianReportData($request);

        try {
            return Excel::download(new TechnicianReportExport($data), 'technician_report_' . now()->format('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            \Log::error('Technician Excel export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Excel report. ' . $e->getMessage());
        }
    }

    /**
     * Export the Technician Report to Word (DOCX).
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */

    public function exportTechnicianWord(Request $request)
    {
        $data = $this->getFilteredTechnicianReportData($request);
        $technicians = $data['technicians'];
        $stats = $data['stats'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $filters = $data['filters'];

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Styles
        $fontStyleTitle = ['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => '2C3E50'];
        $fontStyleSub = ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => '34495E'];
        $fontStyleNormal = ['name' => 'Arial', 'size' => 10, 'color' => '333333'];
        $fontStyleSmall = ['name' => 'Arial', 'size' => 9, 'color' => '555555'];
        $paraStyleCenter = ['align' => 'center'];
        $headerCellStyle = ['bgColor' => 'F5F5F5', 'valign' => 'center'];
        $headerFont = ['bold' => true, 'size' => 10, 'color' => '555555'];

        // Table Style
        $tableStyle = [
            'borderColor' => 'D0D0D0',
            'borderSize'  => 6,
            'cellMargin'  => 80,
            'alignment'   => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'layout'      => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
        ];
        $phpWord->addTableStyle('TechnicianTable', $tableStyle);

        // Header
        $section->addText('OCM - Online Campus Management', $fontStyleSmall, $paraStyleCenter);
        $section->addText('Technician Performance Report', $fontStyleTitle, $paraStyleCenter);
        $section->addText('Generated: ' . now()->format('F j, Y \a\t H:i A T'), $fontStyleSmall, $paraStyleCenter);
        $section->addTextBreak(1);

        // Summary
        $section->addText('Report Summary:', $fontStyleSub);
        $section->addText('Period: ' . $startDate->format('F j, Y') . ' to ' . $endDate->format('F j, Y'), $fontStyleNormal);
        $section->addText('Total Technicians (filtered): ' . $stats['total_technicians'], $fontStyleNormal);
        $section->addText('Total Tasks Assigned (filtered): ' . $stats['total_tasks'], $fontStyleNormal);
        $section->addText('Total Tasks Completed (filtered): ' . $stats['completed_tasks'], $fontStyleNormal);
        $section->addText('Average Completion Rate: ' . number_format($stats['avg_completion_rate'], 2) . '%', $fontStyleNormal);
        $section->addTextBreak(1);

        // Filters
        $section->addText('Filters Applied:', $fontStyleSub);
        $section->addText('Status: ' . ucfirst(str_replace('_', ' ', $filters['status'])), $fontStyleNormal);
        $section->addText('Priority: ' . ucfirst($filters['priority']), $fontStyleNormal);
        $section->addTextBreak(2);

        // Table Title
        $section->addText('Technician Details:', $fontStyleSub);
        $section->addTextBreak(1);

        if ($technicians->isNotEmpty()) {
            $table = $section->addTable('TechnicianTable');
            $table->addRow();

            // Table header cells (adjust width in TWIP; total should be around 9000-9500)
            $table->addCell(1500, $headerCellStyle)->addText('Name', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Specialization', $headerFont, $paraStyleCenter);
            $table->addCell(1000, $headerCellStyle)->addText('Availability', $headerFont, $paraStyleCenter);
            $table->addCell(1000, $headerCellStyle)->addText('Workload', $headerFont, $paraStyleCenter);
            $table->addCell(1000, $headerCellStyle)->addText('Total Tasks', $headerFont, $paraStyleCenter);
            $table->addCell(1000, $headerCellStyle)->addText('Completed Tasks', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Completion Rate', $headerFont, $paraStyleCenter);
            $table->addCell(1000, $headerCellStyle)->addText('Avg Time (Days)', $headerFont, $paraStyleCenter);

            // Table Rows
            foreach ($technicians as $technician) {
                $table->addRow();
                $table->addCell(1500)->addText($technician['name']);
                $table->addCell(1500)->addText($technician['specialization']);
                $table->addCell(1000)->addText($technician['availability']);
                $table->addCell(1000)->addText($technician['workload']);
                $table->addCell(1000)->addText($technician['total_tasks']);
                $table->addCell(1000)->addText($technician['completed_tasks']);
                $table->addCell(1500)->addText(number_format($technician['completion_rate'], 2) . '%');
                $table->addCell(1000)->addText(number_format($technician['avg_completion_time'], 1));
            }
        } else {
            $section->addText('No technicians found matching your criteria.');
        }

        // Output to download
        try {
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $fileName = 'technician_report_' . now()->format('Ymd_His') . '.docx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $objWriter->save('php://output');
            exit;
        } catch (\Exception $e) {
            \Log::error('Technician Word export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Word report. ' . $e->getMessage());
        }
    }

}
