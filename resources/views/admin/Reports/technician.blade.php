@extends('layouts.AdminNavBar')

@section('title', 'Technician Report')

@section('content')
    <div class="container-fluid px-4 py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Technician Performance Report</h1>
                <p class="text-muted mb-0">{{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Export Report
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.report.export.pdf', request()->all()) }}">PDF</a></li>
                    <li><a class="dropdown-item" href="#">Excel</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="window.print()">Print</a></li>
                </ul>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.technicians') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="all" {{ ($filters['priority'] ?? 'all') === 'all' ? 'selected' : '' }}>All Priorities</option>
                                <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.reports.technicians') }}" class="btn btn-outline-secondary ms-2">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card border-start border-primary border-3 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Technicians</h6>
                        <h3 class="card-title">{{ $stats['total_technicians'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-info border-3 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Tasks</h6>
                        <h3 class="card-title">{{ $stats['total_tasks'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-success border-3 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Completed Tasks</h6>
                        <h3 class="card-title">{{ $stats['completed_tasks'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-warning border-3 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Avg Completion Rate</h6>
                        <h3 class="card-title">{{ number_format($stats['avg_completion_rate'], 2) }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Technician List --}}
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Technician Performance</h5>
                <span class="badge bg-light text-dark">{{ count($technicians) }} technicians</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Technician</th>
                            <th>Specialization</th>
                            <th>Availability</th>
                            <th>Workload</th>
                            <th>Tasks</th>
                            <th>Completed</th>
                            <th>Completion Rate</th>
                            <th>Avg Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($technicians as $technician)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $technician['first_name'] }} {{ $technician['last_name'] }}</td>
                                <td>{{ $technician['specialization'] }}</td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($technician['availability_status'] == 'Available') bg-success
                                        @elseif($technician['availability_status'] == 'On Leave') bg-warning
                                        @else bg-secondary
                                        @endif">
                                        {{ $technician['availability_status'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar
                                            @if($technician['current_workload'] < 50) bg-success
                                            @elseif($technician['current_workload'] < 80) bg-warning
                                            @else bg-danger
                                            @endif"
                                             role="progressbar"
                                             style="width: {{ $technician['current_workload'] }}%"
                                             aria-valuenow="{{ $technician['current_workload'] }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $technician['current_workload'] }}%</small>
                                </td>
                                <td>{{ $technician['total_tasks'] }}</td>
                                <td>{{ $technician['completed_tasks'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary"
                                                 role="progressbar"
                                                 style="width: {{ $technician['completion_rate'] }}%"
                                                 aria-valuenow="{{ $technician['completion_rate'] }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="ms-2">{{ number_format($technician['completion_rate'], 1) }}%</small>
                                    </div>
                                </td>
                                <td>{{ $technician['avg_completion_time'] }} days</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <h5>No technicians found</h5>
                                    <p class="text-muted">Try adjusting your filters</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($technicians instanceof \Illuminate\Pagination\LengthAwarePaginator && $technicians->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $technicians->firstItem() }} to {{ $technicians->lastItem() }} of {{ $technicians->total() }} entries
                        </div>
                        <div>
                            {{ $technicians->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table {
            font-size: 0.875rem;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        .progress {
            border-radius: 3px;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }
    </style>
@endpush
