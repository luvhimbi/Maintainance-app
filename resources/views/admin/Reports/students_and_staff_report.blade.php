@extends('Layouts.AdminNavBar')

@section('title', 'Students & Staff Report')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h2 class="h5 mb-1 fw-bold text-dark">Students & Staff Report</h2>
                        <p class="text-muted small mb-0">Overview of student and staff accounts, and their reported issues.</p>
                    </div>
                    <div class="d-flex gap-3 mt-3 mt-md-0">
                    <span class="badge bg-primary-subtle text-primary py-2 px-3 rounded-pill fw-medium">
                        Total Students (filtered): {{ $students->count() }}
                    </span>
                        <span class="badge bg-info-subtle text-info py-2 px-3 rounded-pill fw-medium">
                        Total Staff (filtered): {{ $staffMembers->count() }}
                    </span>
                        <span class="badge bg-success-subtle text-success py-2 px-3 rounded-pill fw-medium">
                        Total Issues Reported (filtered): {{ $totalStudentIssues + $totalStaffIssues }}
                    </span>
                    </div>
                </div>
                <hr class="my-3"> {{-- Separator for filters --}}
                <form action="{{ route('admin.reports.students_and_staff') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="search" class="form-label small text-muted mb-0">Search</label>
                        <input type="text" class="form-control form-control-sm rounded-pill @error('search') is-invalid @enderror" id="search" name="search" placeholder="Name, Email, Student No., Dept..." value="{{ old('search', $searchTerm ?? '') }}">
                        @error('search')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-auto">
                        <label for="start_date" class="form-label small text-muted mb-0">Start Date</label>
                        <input type="date" class="form-control form-control-sm rounded-pill @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $startDate ?? '') }}">
                        @error('start_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-auto">
                        <label for="end_date" class="form-label small text-muted mb-0">End Date</label>
                        <input type="date" class="form-control form-control-sm rounded-pill @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $endDate ?? '') }}">
                        @error('end_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                        @if($searchTerm || $startDate || $endDate)
                            <a href="{{ route('admin.reports.students_and_staff') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 ms-2"><i class="fas fa-times me-1"></i> Clear Filters</a>
                        @endif
                    </div>
                </form>
                <hr class="my-3"> {{-- Separator for export buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-success btn-sm rounded-pill px-3 dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-export me-1"></i> Export Data
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.reports.export_pdf', request()->query()) }}"><i class="fas fa-file-pdf me-2"></i> Export to PDF</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.export_excel', request()->query()) }}"><i class="fas fa-file-excel me-2"></i> Export to Excel</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.reports.export_word', request()->query()) }}"><i class="fas fa-file-word me-2"></i> Export to Word</a></li>
                            <li><a class="dropdown-item" href="#" id="exportJpg"><i class="fas fa-file-image me-2"></i> Export to JPG</a></li>
                            <li><hr class="dropdown-divider"></li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="mb-5">
                    <h3 class="h6 fw-bold text-dark mb-3 d-flex align-items-center">
                    <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-user-graduate"></i>
                    </span>
                        Students ({{ $students->count() }})
                    </h3>
                    @if($students->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Name</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Student No.</th>
                                    <th class="py-2">Course</th>
                                    <th class="py-2 text-center">Issues Reported</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    <tr class="border-top">
                                        <td class="ps-3 text-dark fw-medium">{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td class="text-primary fw-medium">{{ $student->email }}</td>
                                        <td class="text-muted">{{ $student->studentDetail->student_number ?? 'N/A' }}</td>
                                        <td class="text-muted">{{ $student->studentDetail->course ?? 'N/A' }}</td>
                                        <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary fw-medium py-1 px-2 rounded-pill">
                                            {{ $student->issues->count() }}
                                        </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info rounded-3 shadow-sm text-center py-3">
                            <i class="fas fa-info-circle me-2"></i> No student accounts found matching your criteria.
                        </div>
                    @endif
                </div>

                <div>
                    <h3 class="h6 fw-bold text-dark mb-3 d-flex align-items-center">
                    <span class="bg-info bg-opacity-10 text-info p-2 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-user-tie"></i>
                    </span>
                        Staff Members ({{ $staffMembers->count() }})
                    </h3>
                    @if($staffMembers->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Name</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Department</th>
                                    <th class="py-2">Position</th>
                                    <th class="py-2 text-center">Issues Reported</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($staffMembers as $staff)
                                    <tr class="border-top">
                                        <td class="ps-3 text-dark fw-medium">{{ $staff->first_name }} {{ $staff->last_name }}</td>
                                        <td class="text-primary fw-medium">{{ $staff->email }}</td>
                                        <td class="text-muted">{{ $staff->staffDetail->department ?? 'N/A' }}</td>
                                        <td class="text-muted">{{ $staff->staffDetail->position_title ?? 'N/A' }}</td>
                                        <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary fw-medium py-1 px-2 rounded-pill">
                                            {{ $staff->issues->count() }}
                                        </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info rounded-3 shadow-sm text-center py-3">
                            <i class="fas fa-info-circle me-2"></i> No staff member accounts found matching your criteria.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            body {
                background-color: #f8f9fa;
                font-family: 'Inter', sans-serif;
            }
            .card {
                border: 1px solid #e0e0e0;
                box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
            }
            .card-header {
                background-color: #ffffff;
                color: #343a40;
                border-bottom: 1px solid #e9ecef;
            }
            .card-header h2, .card-header p {
                color: #343a40 !important;
            }
            .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
            .text-primary { color: #0d6efd !important; }
            .bg-success-subtle { background-color: rgba(40, 167, 69, 0.1) !important; }
            .text-success { color: #28a745 !important; }
            .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
            .text-warning { color: #ffc107 !important; }
            .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
            .text-danger { color: #dc3545 !important; }
            .bg-info-subtle { background-color: rgba(23, 162, 184, 0.1) !important; }
            .text-info { color: #17a2b8 !important; }
            .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
            .text-secondary { color: #6c757d !important; }
            .table thead th {
                font-weight: 600;
                color: #495057;
                border-bottom: 2px solid #e9ecef;
            }
            .table tbody tr {
                transition: background-color 0.2s ease;
            }
            .table tbody tr:hover {
                background-color: #f0f2f5;
            }
            .badge {
                font-weight: 500;
            }
            .form-control-sm.rounded-pill {
                border-radius: 2rem !important;
                padding: 0.25rem 0.75rem;
            }
            /* Style for validation feedback */
            .invalid-feedback {
                font-size: 0.875em; /* Match Bootstrap's small font size */
                margin-top: 0.25rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('exportJpg').addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default link behavior

                    // Target the main card element for capture
                    const reportCard = document.querySelector('.card');

                    if (reportCard) {
                        html2canvas(reportCard, {
                            scale: 2, // Increase scale for better resolution
                            logging: false, // Disable logging for cleaner console
                            useCORS: true // Enable CORS if you have external images/fonts
                        }).then(canvas => {
                            const link = document.createElement('a');
                            link.download = 'students_and_staff_report.jpg';
                            link.href = canvas.toDataURL('image/jpeg', 0.9); // 0.9 quality for JPG
                            link.click();
                        }).catch(error => {
                            console.error('Error generating JPG:', error);
                            alert('Failed to generate JPG report. Please try again.');
                        });
                    } else {
                        alert('Report content not found for JPG export.');
                    }
                });
            });
        </script>
    @endpush
@endsection
