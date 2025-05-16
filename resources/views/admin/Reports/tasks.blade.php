@extends('layouts.AdminNavBar')

@section('title', 'Task Report')

@section('content')
    <div class="container-fluid px-4 py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Maintenance Task Report</h1>
                <p class="text-muted mb-0">{{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Export Report
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.reports.tasks.export.pdf', request()->all()) }}">PDF</a></li>
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
                <form method="GET" action="{{ route('admin.reports.tasks') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="priority" class="form-label">Priority</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="all" {{ ($filters['priority'] ?? 'all') === 'all' ? 'selected' : '' }}>All Priorities</option>
                                <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                Apply Filters
                            </button>
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
                        <h6 class="card-subtitle mb-2 text-muted">Total Tasks</h6>
                        <h3 class="card-title">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-success border-3 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Completed</h6>
                        <h3 class="card-title">{{ $stats['completed'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-warning border-3 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Pending</h6>
                        <h3 class="card-title">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-danger border-3 h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Overdue</h6>
                        <h3 class="card-title">{{ $stats['overdue'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Task List --}}
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Task Details</h5>
                <span class="badge bg-light text-dark">{{ $tasks->total() }} tasks</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Task ID</th>
                            <th>Issue Type</th>
                            <th>Location</th>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Due Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($tasks as $task)
                            <tr class="{{ $task->expected_completion < now() && $task->issue_status != 'Completed' ? 'table-warning' : '' }}">
                                <td class="fw-bold">#{{ $task->task_id }}</td>
                                <td>{{ $task->issue->issue_type }}</td>
                                <td>
                                    @if($task->issue->location)
                                        {{ $task->issue->location->building_name }} (Room {{ $task->issue->location->room_number }})
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->assignee)
                                        {{ $task->assignee->first_name }} {{ $task->assignee->last_name }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($task->issue_status == 'completed') bg-success
                                        @elseif($task->issue_status == 'pending') bg-secondary
                                        @else bg-primary
                                        @endif">
                                        {{ ucfirst($task->issue_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="{{ $task->expected_completion < now() && $task->issue_status != 'Completed' ? 'text-danger fw-bold' : '' }}">
                                        {{ $task->expected_completion->format('M d, Y') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <h5>No tasks found</h5>
                                    <p class="text-muted">Try adjusting your filters</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($tasks->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} entries
                        </div>
                        <div>
                            {{ $tasks->withQueryString()->links('pagination::bootstrap-5') }}
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

        .table-warning {
            background-color: rgba(255, 193, 7, 0.05);
        }
    </style>
@endpush
