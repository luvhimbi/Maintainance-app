<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsAndStaffExport;
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
use App\Exports\TaskExport;
use App\Exports\TechnicianReportExport;
use App\Exports\PerformanceReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{


// this is for display data initially
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

        // 2. Create a new PhpWord object
        $phpWord = new PhpWord();

        // 3. Add General Report Information Section
        $section = $phpWord->addSection();

        // System Name & Report Title
        $section->addText('OCM - Online Campus Management', ['size' => 10, 'color' => '888888'], ['align' => 'center']);
        $section->addText('Students & Staff Report', ['name' => 'Arial', 'size' => 16, 'bold' => true], ['align' => 'center']);
        $section->addText('Report Generated: ' . now()->format('F j, Y \a\t H:i A T'), ['size' => 10, 'color' => '888888'], ['align' => 'center']);
        $section->addTextBreak(1); // Add a line break

        // Filter & Summary Information
        $section->addText('Report Parameters:', ['size' => 12, 'bold' => true]);
        if ($startDate && $endDate) {
            $section->addText('Date Range: ' . \Carbon\Carbon::parse($startDate)->format('F j, Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('F j, Y'));
        }
        if ($searchTerm) {
            $section->addText('Search Term: "' . $searchTerm . '"');
        }
        $section->addText('Total Students (filtered): ' . $students->count());
        $section->addText('Total Staff (filtered): ' . $staffMembers->count());
        $section->addText('Total Issues Reported (overall): ' . ($totalStudentIssues + $totalStaffIssues));
        $section->addTextBreak(1);

        // 4. Add Students Section
        $section->addText('Students Overview', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
        $section->addTextBreak(1);

        if ($students->isNotEmpty()) {
            $tableStyle = [
                'borderColor' => '000000',
                'borderSize' => 6,
                'cellMargin' => 80,
                'valign' => 'center',
            ];
            $firstRowStyle = ['bgColor' => 'EEEEEE'];
            $cellStyle = ['valign' => 'center'];
            $fontStyle = ['bold' => true, 'size' => 10];
            $paragraphStyle = ['align' => 'center'];

            $table = $section->addTable($tableStyle);
            $table->addRow(400, $firstRowStyle); // Row height
            $table->addCell(1500, $cellStyle)->addText('Name', $fontStyle, $paragraphStyle);
            $table->addCell(2500, $cellStyle)->addText('Email', $fontStyle, $paragraphStyle);
            $table->addCell(1500, $cellStyle)->addText('Student No.', $fontStyle, $paragraphStyle);
            $table->addCell(1500, $cellStyle)->addText('Course', $fontStyle, $paragraphStyle);
            $table->addCell(1500, $cellStyle)->addText('Issues Reported', $fontStyle, $paragraphStyle);

            foreach ($students as $student) {
                $table->addRow();
                $table->addCell(1500)->addText($student->first_name . ' ' . $student->last_name);
                $table->addCell(2500)->addText($student->email);
                $table->addCell(1500)->addText($student->studentDetail->student_number ?? 'N/A');
                $table->addCell(1500)->addText($student->studentDetail->course ?? 'N/A');
                $table->addCell(1500)->addText($student->issues->count(), ['align' => 'center']);
            }
        } else {
            $section->addText('No student accounts found matching your criteria.');
        }

        $section->addTextBreak(2); // Add more space between sections

        // 5. Add Staff Members Section
        $section->addText('Staff Members Overview', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
        $section->addTextBreak(1);

        if ($staffMembers->isNotEmpty()) {
            // Re-use table styles or define new ones if needed
            $table = $section->addTable($tableStyle);
            $table->addRow(400, $firstRowStyle);
            $table->addCell(1500, $cellStyle)->addText('Name', $fontStyle, $paragraphStyle);
            $table->addCell(2500, $cellStyle)->addText('Email', $fontStyle, $paragraphStyle);
            $table->addCell(1500, $cellStyle)->addText('Department', $fontStyle, $paragraphStyle);
            $table->addCell(1500, $cellStyle)->addText('Position', $fontStyle, $paragraphStyle);
            $table->addCell(1500, $cellStyle)->addText('Issues Reported', $fontStyle, $paragraphStyle);

            foreach ($staffMembers as $staff) {
                $table->addRow();
                $table->addCell(1500)->addText($staff->first_name . ' ' . $staff->last_name);
                $table->addCell(2500)->addText($staff->email);
                $table->addCell(1500)->addText($staff->staffDetail->department ?? 'N/A');
                $table->addCell(1500)->addText($staff->staffDetail->position_title ?? 'N/A');
                $table->addCell(1500)->addText($staff->issues->count(), ['align' => 'center']);
            }
        } else {
            $section->addText('No staff member accounts found matching your criteria.');
        }

        // 6. Save the document and send as download
        try {
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007'); // Creates a Word2007 writer (DOCX)
            $fileName = 'students_and_staff_report_' . now()->format('Ymd_His') . '.docx';

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            // Output the document directly to the browser
            $objWriter->save('php://output');
            exit; // Important to exit after sending file
        } catch (\Exception $e) {
            \Log::error('Word export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Word report. ' . $e->getMessage());
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
        // Validate inputs
        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => ['nullable', 'string', Rule::in(['all', 'completed', 'pending', 'in_progress', 'overdue'])],
            'priority' => ['nullable', 'string', Rule::in(['all', 'high', 'medium', 'low'])],
        ]);

        // Ensure start_date and end_date are Carbon instances, with defaults if not provided
        $startDateInput = $validatedData['start_date'] ?? null;
        $endDateInput = $validatedData['end_date'] ?? null;

        // Convert to Carbon instances. If null, use default range.
        $startDate = $startDateInput ? Carbon::parse($startDateInput)->startOfDay() : Carbon::today()->subMonths(1)->startOfDay();
        $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::today()->endOfDay();

        $statusFilter = $validatedData['status'] ?? 'all';
        $priorityFilter = $validatedData['priority'] ?? 'all';

        // Base query for Maintenance Tasks
        $query = Task::with(['issue.location', 'assignee'])
            ->whereBetween('created_at', [$startDate, $endDate]); // Use Carbon instances directly

        // Apply status filter
        if ($statusFilter !== 'all') {
            if ($statusFilter === 'overdue') {
                $query->where('expected_completion', '<', now())
                    ->where('issue_status', '!=', 'Completed'); // Assuming 'Completed' is capitalized in DB
            } else {
                // Convert to capitalized format for DB comparison
                $query->where('issue_status', ucfirst(str_replace('_', ' ', $statusFilter)));
            }
        }

        // Apply priority filter
        if ($priorityFilter !== 'all') {
            $query->whereHas('issue', function ($q) use ($priorityFilter) {
                // Convert to capitalized format for DB comparison
                $q->where('urgency_level', ucfirst($priorityFilter));
            });
        }

        // Get all tasks for stats calculation (without pagination)
        $allFilteredTasks = $query->get();

        // Calculate statistics
        $stats = [
            'total' => $allFilteredTasks->count(),
            'completed' => $allFilteredTasks->where('issue_status', 'Completed')->count(), // Use capitalized
            'pending' => $allFilteredTasks->where('issue_status', 'Pending')->count(),     // Use capitalized
            'in_progress' => $allFilteredTasks->where('issue_status', 'In Progress')->count(), // Use capitalized
            'overdue' => $allFilteredTasks->filter(function ($task) {
                return $task->expected_completion < now() && $task->issue_status != 'Completed'; // Use capitalized
            })->count(),
        ];

        // Apply pagination if requested
        $tasks = $paginate ? $query->paginate(10) : $query->get(); // Paginate for display, get all for export

        return [
            'tasks' => $tasks,
            'stats' => $stats,
            'startDate' => $startDate, // Carbon instance (will never be null now)
            'endDate' => $endDate,     // Carbon instance (will never be null now)
            'filters' => [
                'start_date' => $startDateInput, // Original string for form input value
                'end_date' => $endDateInput,     // Original string for form input value
                'status' => $statusFilter,
                'priority' => $priorityFilter,
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

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Styles
        $fontStyleTitle = ['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => '2C3E50'];
        $fontStyleSub = ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => '34495E'];
        $fontStyleNormal = ['name' => 'Arial', 'size' => 10, 'color' => '333333'];
        $fontStyleSmall = ['name' => 'Arial', 'size' => 9, 'color' => '555555'];
        $paraStyleCenter = ['align' => 'center'];
        $tableStyle = [
            'borderColor' => 'D0D0D0',
            'borderSize'  => 6,
            'cellMargin'  => 80,
            'valign'      => 'center',
            'unit'        => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT, // Use percentage width
            'width'       => 10000, // 100%
        ];
        $headerCellStyle = ['bgColor' => 'F5F5F5', 'valign' => 'center'];
        $headerFont = ['bold' => true, 'size' => 10, 'color' => '555555'];

        // Header
        $section->addText('OCM - Online Campus Management', $fontStyleSmall, $paraStyleCenter);
        $section->addText('Maintenance Task Report', $fontStyleTitle, $paraStyleCenter);
        $section->addText('Generated: ' . now()->format('F j, Y \a\t H:i A T'), $fontStyleSmall, $paraStyleCenter);
        $section->addTextBreak(1);

        // Summary
        $section->addText('Report Summary:', $fontStyleSub);
        $section->addText('Period: ' . $startDate->format('F j, Y') . ' to ' . $endDate->format('F j, Y'), $fontStyleNormal);
        $section->addText('Total Tasks: ' . $stats['total'], $fontStyleNormal);
        $section->addText('Completed Tasks: ' . $stats['completed'], $fontStyleNormal);
        $section->addText('Pending Tasks: ' . $stats['pending'], $fontStyleNormal);
        $section->addText('In Progress Tasks: ' . $stats['in_progress'], $fontStyleNormal);
        $section->addText('Overdue Tasks: ' . $stats['overdue'], $fontStyleNormal);
        $section->addTextBreak(1);

        // Filters Applied
        $section->addText('Filters Applied:', $fontStyleSub);
        $section->addText('Status: ' . ucfirst(str_replace('_', ' ', $filters['status'])), $fontStyleNormal);
        $section->addText('Priority: ' . ucfirst($filters['priority']), $fontStyleNormal);
        $section->addTextBreak(2);

        // Task Details Table
        $section->addText('Task Details:', $fontStyleSub);
        $section->addTextBreak(1);

        if ($tasks->isNotEmpty()) {
            $table = $section->addTable($tableStyle);
            $table->addRow(400); // Header row height

            // Table Headers
            $table->addCell(1500, $headerCellStyle)->addText('Task ID', $headerFont, $paraStyleCenter);
            $table->addCell(2000, $headerCellStyle)->addText('Issue Type', $headerFont, $paraStyleCenter);
            $table->addCell(2500, $headerCellStyle)->addText('Location', $headerFont, $paraStyleCenter);
            $table->addCell(2000, $headerCellStyle)->addText('Assignee', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Status', $headerFont, $paraStyleCenter);
            $table->addCell(2000, $headerCellStyle)->addText('Due Date', $headerFont, $paraStyleCenter);

            // Table Rows
            foreach ($tasks as $task) {
                $table->addRow();
                $table->addCell(1500)->addText('#' . $task->task_id);
                $table->addCell(2000)->addText($task->issue->issue_type ?? 'N/A');
                $table->addCell(2500)->addText(
                    ($task->issue->location->building_name ?? 'N/A') .
                    ($task->issue->location->room_number ? ' (Room ' . $task->issue->location->room_number . ')' : '')
                );
                $table->addCell(2000)->addText($task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned');

                $statusText = ucfirst(str_replace('_', ' ', $task->issue_status));
                $statusColor = '000000'; // Default black
                if ($task->issue_status == 'completed') $statusColor = '28A745'; // Green
                elseif ($task->issue_status == 'pending') $statusColor = 'FFC107'; // Yellow
                elseif ($task->issue_status == 'in_progress') $statusColor = '0D6EFD'; // Blue
                else if ($task->expected_completion < now() && $task->issue_status != 'completed') $statusColor = 'DC3545'; // Red for overdue

                $table->addCell(1500)->addText($statusText, ['color' => $statusColor, 'bold' => true], $paraStyleCenter);

                $dueDateText = $task->expected_completion->format('M d, Y');
                $dueDateColor = '000000';
                if ($task->expected_completion < now() && $task->issue_status != 'completed') {
                    $dueDateColor = 'DC3545'; // Red for overdue
                }
                $table->addCell(2000)->addText($dueDateText, ['color' => $dueDateColor, 'bold' => true]);
            }
        } else {
            $section->addText('No tasks found matching your criteria.');
        }


        // Save the document and send as download
        try {
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $fileName = 'maintenance_task_report_' . now()->format('Ymd_His') . '.docx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $objWriter->save('php://output');
            exit;
        } catch (\Exception $e) {
            \Log::error('Task Word export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Word report. ' . $e->getMessage());
        }
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
        // Validate inputs
        $validatedData = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => ['nullable', 'string', Rule::in(['all', 'completed', 'pending', 'in_progress'])], // 'overdue' not applicable here as it's a derived status
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
            ->with(['maintenanceStaff', 'tasks' => function ($query) use ($startDate, $endDate, $statusFilter, $priorityFilter) {
                $query->whereBetween('assignment_date', [$startDate, $endDate])
                    ->when($statusFilter !== 'all', function ($q) use ($statusFilter) {
                        // Assuming issue_status in DB is 'Completed', 'Pending', 'In Progress'
                        $q->where('issue_status', ucfirst(str_replace('_', ' ', $statusFilter)));
                    })
                    ->when($priorityFilter !== 'all', function ($q) use ($priorityFilter) {
                        // Assuming priority in DB is 'High', 'Medium', 'Low'
                        $q->whereHas('issue', function($issueQuery) use ($priorityFilter) {
                            $issueQuery->where('urgency_level', ucfirst($priorityFilter));
                        });
                    });
            }])
            ->get()
            ->map(function ($technician) use ($startDate, $endDate) {
                // Filter tasks again to ensure only those within the date range and status/priority apply to counts
                $filteredTasks = $technician->tasks->filter(function($task) use ($startDate, $endDate) {
                    return $task->assignment_date->between($startDate, $endDate);
                });

                $completedTasks = $filteredTasks->where('issue_status', 'Completed'); // Assuming 'Completed' is capitalized in DB
                $totalTasks = $filteredTasks->count();

                $avgCompletionTime = null;
                if ($completedTasks->isNotEmpty()) {
                    $totalCompletionTimeInDays = 0;
                    foreach ($completedTasks as $task) {
                        // Calculate time from assignment_date to actual_completion_date
                        // If actual_completion_date is null, use expected_completion or current time
                        $completionDate = $task->actual_completion_date ?? $task->expected_completion ?? Carbon::now();
                        $totalCompletionTimeInDays += Carbon::parse($task->assignment_date)->diffInDays($completionDate);
                    }
                    $avgCompletionTime = $totalCompletionTimeInDays / $completedTasks->count();
                }

                return [
                    'id' => $technician->id,
                    'name' => $technician->first_name . ' ' . $technician->last_name,
                    'email' => $technician->email, // Include email for Excel/Word
                    'phone_number' => $technician->phone_number, // Include phone for Excel/Word
                    'address' => $technician->address, // Include address for Excel/Word
                    'specialization' => $technician->maintenanceStaff->specialization ?? 'N/A',
                    'availability' => $technician->maintenanceStaff->availability_status ?? 'Unknown',
                    'workload' => $technician->maintenanceStaff->current_workload ?? 0,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks->count(),
                    'completion_rate' => $totalTasks > 0
                        ? round(($completedTasks->count() / $totalTasks) * 100, 2)
                        : 0,
                    'avg_completion_time' => $avgCompletionTime ? round($avgCompletionTime, 1) : 0,
                ];
            })
            ->filter(function($technician) {
                // Only include technicians who have tasks after filtering
                return $technician['total_tasks'] > 0;
            });


        // Calculate overall statistics
        $totalTechnicians = $technicians->count();
        $totalTasksOverall = $technicians->sum('total_tasks');
        $completedTasksOverall = $technicians->sum('completed_tasks');
        $avgCompletionRateOverall = $totalTasksOverall > 0
            ? round(($completedTasksOverall / $totalTasksOverall) * 100, 2)
            : 0;

        $stats = [
            'total_technicians' => $totalTechnicians,
            'total_tasks' => $totalTasksOverall,
            'completed_tasks' => $completedTasksOverall,
            'avg_completion_rate' => $avgCompletionRateOverall,
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
                'priority' => $priorityFilter,
            ],
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
        $tableStyle = [
            'borderColor' => 'D0D0D0',
            'borderSize'  => 6,
            'cellMargin'  => 80,
            'valign'      => 'center',
            'unit'        => \PhpOffice\PhpWord\SimpleType\TblWidth::PERCENT,
            'width'       => 10000,
        ];
        $headerCellStyle = ['bgColor' => 'F5F5F5', 'valign' => 'center'];
        $headerFont = ['bold' => true, 'size' => 10, 'color' => '555555'];

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

        // Filters Applied
        $section->addText('Filters Applied:', $fontStyleSub);
        $section->addText('Status: ' . ucfirst(str_replace('_', ' ', $filters['status'])), $fontStyleNormal);
        $section->addText('Priority: ' . ucfirst($filters['priority']), $fontStyleNormal);
        $section->addTextBreak(2);

        // Technician Details Table
        $section->addText('Technician Details:', $fontStyleSub);
        $section->addTextBreak(1);

        if ($technicians->isNotEmpty()) {
            $table = $section->addTable($tableStyle);
            $table->addRow(400); // Header row height

            // Table Headers
            $table->addCell(1500, $headerCellStyle)->addText('Name', $headerFont, $paraStyleCenter);
            $table->addCell(2000, $headerCellStyle)->addText('Specialization', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Availability', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Workload', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Total Tasks', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Completed Tasks', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Completion Rate', $headerFont, $paraStyleCenter);
            $table->addCell(1500, $headerCellStyle)->addText('Avg Time (Days)', $headerFont, $paraStyleCenter);

            // Table Rows
            foreach ($technicians as $technician) {
                $table->addRow();
                $table->addCell(1500)->addText($technician['name']);
                $table->addCell(2000)->addText($technician['specialization']);
                $table->addCell(1500)->addText($technician['availability']);
                $table->addCell(1500)->addText($technician['workload']);
                $table->addCell(1500)->addText($technician['total_tasks']);
                $table->addCell(1500)->addText($technician['completed_tasks']);
                $table->addCell(1500)->addText(number_format($technician['completion_rate'], 2) . '%');
                $table->addCell(1500)->addText(number_format($technician['avg_completion_time'], 1));
            }
        } else {
            $section->addText('No technicians found matching your criteria.');
        }

        // Save the document and send as download
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
