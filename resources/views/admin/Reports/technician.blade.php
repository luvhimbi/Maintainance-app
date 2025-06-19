@extends('Layouts.AdminNavBar')

@section('title', 'Technician Report')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h1 class="h5 fw-bold mb-1 text-dark">Technician Performance Report</h1>
                        <p class="text-muted small mb-0">Overview of technician performance from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
                    </div>
                </div>
                <hr class="my-3"> {{-- Separator for filters --}}
                <form method="GET" action="{{ route('admin.reports.technicians') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="start_date" class="form-label small text-muted mb-0">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-pill" value="{{ $filters['start_date'] ?? '' }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="end_date" class="form-label small text-muted mb-0">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-pill" value="{{ $filters['end_date'] ?? '' }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="status" class="form-label small text-muted mb-0">Task Status</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-pill">
                            <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ ($filters['status'] ?? '') === 'in progress' ? 'selected' : '' }}>In Progress</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="priority" class="form-label small text-muted mb-0">Task Priority</label>
                        <select name="priority" id="priority" class="form-select form-select-sm rounded-pill">
                            <option value="all" {{ ($filters['priority'] ?? 'all') === 'all' ? 'selected' : '' }}>All Priorities</option>
                            <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex align-items-end justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 me-2">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.reports.technicians') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fas fa-times me-1"></i> Clear Filters
                        </a>
                    </div>
                </form>
                <hr class="my-3"> {{-- Separator for export buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-success btn-sm rounded-pill px-3 dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-export me-1"></i> Export Data
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="{{ route('technicians.export.pdf', request()->query()) }}"><i class="fas fa-file-pdf me-2"></i> Export to PDF</a></li>
                            <li><a class="dropdown-item" href="{{ route('technicians.export.excel', request()->query()) }}"><i class="fas fa-file-excel me-2"></i> Export to Excel</a></li>
                            <li><a class="dropdown-item" href="{{ route('technicians.export.word', request()->query()) }}"><i class="fas fa-file-word me-2"></i> Export to Word</a></li>
                            <li><a class="dropdown-item" href="#" id="exportTechnicianJpg"><i class="fas fa-file-image me-2"></i> Export to JPG</a></li>


                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 pt-0">
                {{-- Summary Cards --}}
                <div class="row mb-4 g-3">
                    <div class="col-md-3">
                        <div class="card border-start border-primary border-3 h-100 shadow-sm-hover">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Total Technicians (Filtered)</h6>
                                <h3 class="card-title text-primary">{{ $stats['total_technicians'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-info border-3 h-100 shadow-sm-hover">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Total Tasks Assigned (Filtered)</h6>
                                <h3 class="card-title text-info">{{ $stats['total_tasks'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-success border-3 h-100 shadow-sm-hover">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Total Tasks Completed (Filtered)</h6>
                                <h3 class="card-title text-success">{{ $stats['completed_tasks'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-warning border-3 h-100 shadow-sm-hover">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Avg Completion Rate (Filtered)</h6>
                                <h3 class="card-title text-warning">{{ number_format($stats['avg_completion_rate'], 2) }}%</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Technician List --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white rounded-top-4">
                        <h5 class="mb-0 fw-bold text-dark">Technician Details</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Name</th>
                                    <th class="py-2">Specialization</th>
                                    <th class="py-2">Availability</th>
                                    <th class="py-2">Workload</th>
                                    <th class="py-2 text-center">Total Tasks</th>
                                    <th class="py-2 text-center">Completed Tasks</th>
                                    <th class="py-2 text-center">Completion Rate</th>
                                    <th class="py-2 text-center">Avg Completion Time (Days)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($technicians as $technician)
                                    <tr>
                                        <td class="ps-3 text-dark fw-medium">{{ $technician['name'] }}</td>
                                        <td>{{ $technician['specialization'] }}</td>
                                        <td>
                                            <span class="badge rounded-pill
                                                @if($technician['availability'] == 'Available') bg-success-subtle text-success
                                                @elseif($technician['availability'] == 'Busy') bg-warning-subtle text-warning
                                                @else bg-secondary-subtle text-secondary
                                                @endif fw-medium py-1 px-2">
                                                {{ $technician['availability'] }}
                                            </span>
                                        </td>
                                        <td>{{ $technician['workload'] }}</td>
                                        <td class="text-center">{{ $technician['total_tasks'] }}</td>
                                        <td class="text-center">{{ $technician['completed_tasks'] }}</td>
                                        <td class="text-center">{{ number_format($technician['completion_rate'], 2) }}%</td>
                                        <td class="text-center">{{ number_format($technician['avg_completion_time'], 1) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <h5 class="text-muted">No technicians found</h5>
                                            <p class="text-muted mb-0">No technicians found matching your criteria or with reported tasks in the selected period.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endsection

        @push('styles')
            <style>
                body {
                    background-color: #f8f9fa;
                    font-family: 'Inter', sans-serif;
                }
                .card {
                    border: 1px solid #e0e0e0;
                    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.03);
                }
                .card-header {
                    background-color: #ffffff;
                    color: #343a40;
                    border-bottom: 1px solid #e9ecef;
                }
                .card-header h1, .card-header h5, .card-header p {
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
                .form-control-sm.rounded-pill, .form-select-sm.rounded-pill {
                    border-radius: 2rem !important;
                    padding: 0.25rem 0.75rem;
                }
                .shadow-sm-hover:hover {
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
                    transition: box-shadow 0.3s ease-in-out;
                }
                .table-danger-light { /* For overdue tasks, though not directly used in this report's table rows */
                    background-color: #fff3f5;
                }
            </style>
        @endpush

        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('exportTechnicianJpg').addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent default link behavior

                        // Target the main card element for capture
                        const reportCard = document.querySelector('.card'); // This targets the outermost card

                        if (reportCard) {
                            html2canvas(reportCard, {
                                scale: 2,           // Increase scale for better resolution
                                logging: false,     // Disable logging
                                useCORS: true       // Enable CORS for images if any
                            }).then(canvas => {
                                const link = document.createElement('a');
                                link.download = 'technician_performance_report.jpg';
                                link.href = canvas.toDataURL('image/jpeg', 0.9); // 0.9 quality
                                link.click();
                            }).catch(error => {
                                console.error('Error generating JPG:', error);
                                alert('Failed to generate JPG report. Please try again.');
                            });
                        } else {
                            alert('Report content not found for JPG export. Please check the JavaScript selector.');
                        }
                    });
                });
            </script>
    @endpush
