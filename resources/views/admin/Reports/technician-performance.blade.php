@extends('Layouts.AdminNavBar')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>Technician Performance Analysis
                </h4>
                <form method="GET" class="row g-2">
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="Pending" {{ $statusFilter == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ $statusFilter == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ $statusFilter == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="priority" class="form-select form-select-sm">
                            <option value="all" {{ $priorityFilter == 'all' ? 'selected' : '' }}>All Priorities</option>
                            <option value="High" {{ $priorityFilter == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Medium" {{ $priorityFilter == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="Low" {{ $priorityFilter == 'Low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="technician_id" class="form-select form-select-sm">
                            <option value="all" {{ $technicianId == 'all' ? 'selected' : '' }}>All Technicians</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $technicianId == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            @if($technicians->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>No technicians match the selected filters.
                </div>
            @else
                <div class="row mb-4">
                    <!-- Summary Cards -->
                    <div class="col-md-3 mb-3">
                        <div class="card border-start border-primary border-3 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Total Tasks</h6>
                                <h3 class="mb-0">{{ $technicians->sum('total_tasks') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-start border-success border-3 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Completed</h6>
                                <h3 class="mb-0">{{ $technicians->sum('completed_tasks') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-start border-warning border-3 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">In Progress</h6>
                                <h3 class="mb-0">{{ $technicians->sum('in_progress_tasks') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-start border-danger border-3 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">High Priority</h6>
                                <h3 class="mb-0">{{ $technicians->sum('high_priority_tasks') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Technician</th>
                                <th class="text-center">Tasks</th>
                                <th class="text-center">Completed</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">In Progress</th>
                                <th class="text-center">High Priority</th>
                                <th class="text-center">Performance Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($technicians as $tech)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <span class="avatar-title rounded-circle bg-light-primary text-primary">
                                                {{ strtoupper(substr($tech->username, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $tech->username }}</h6>
                                            <small class="text-muted">{{ $tech->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $tech->total_tasks }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $tech->completed_tasks }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning">{{ $tech->pending_tasks }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $tech->in_progress_tasks }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ $tech->high_priority_tasks }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar 
                                            @if($tech->performance_score >= 80) bg-success
                                            @elseif($tech->performance_score >= 50) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ abs($tech->performance_score) }}%" 
                                            aria-valuenow="{{ $tech->performance_score }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            {{ $tech->performance_score }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .avatar-sm {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .progress {
        border-radius: 10px;
        background-color: #f0f0f0;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .border-3 {
        border-width: 3px !important;
    }
</style>
@endsection