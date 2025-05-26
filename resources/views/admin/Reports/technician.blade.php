@extends('layouts.AdminNavBar')

@section('title', 'Technician Report')

@section('content')
<div class="container-fluid px-4 py-3">
    <h1>Technician Report</h1>
    <p>Report Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.reports.technicians') }}" class="mb-4">
        <div class="row">
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
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'All' ? 'selected' : '' }}>All</option>
                    <option value="completed" {{ ($filters['status'] ?? '') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ ($filters['status'] ?? '') === 'In progress' ? 'selected' : '' }}>In Progress</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="priority" class="form-label">Priority</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="all" {{ ($filters['priority'] ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                    <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </div>
    </form>

   
    <div class="mb-4">
        <a href="{{ route('admin.report.export.pdf', request()->all()) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-2"></i>Generate PDF
        </a>
       
    {{-- Summary --}}
    <div class="card mb-4">
        <div class="card-header">Summary</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Technicians</h5>
                            <p class="card-text display-6">{{ $stats['total_technicians'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Tasks</h5>
                            <p class="card-text display-6">{{ $stats['total_tasks'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Completed Tasks</h5>
                            <p class="card-text display-6">{{ $stats['completed_tasks'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Avg Completion Rate</h5>
                            <p class="card-text display-6">{{ number_format($stats['avg_completion_rate'], 2) }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Technician List --}}
    <div class="card">
        <div class="card-header">Technicians</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Availability</th>
                        <th>Workload</th>
                        <th>Total Tasks</th>
                        <th>Completed Tasks</th>
                        <th>Completion Rate</th>
                        <th>Avg Completion Time (Days)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($technicians as $technician)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $technician['first_name'] }} {{ $technician['last_name'] }}</td>
                            <td>{{ $technician['specialization'] }}</td>
                            <td>{{ $technician['availability_status'] }}</td>
                            <td>{{ $technician['current_workload'] }}</td>
                            <td>{{ $technician['total_tasks'] }}</td>
                            <td>{{ $technician['completed_tasks'] }}</td>
                            <td>{{ number_format($technician['completion_rate'], 2) }}%</td>
                            <td>{{ $technician['avg_completion_time'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No technicians found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection