
@extends('layouts.AdminNavBar')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Dashboard Overview</h2>
            <p class="text-muted">System statistics and key metrics</p>
        </div>
        <div class="badge bg-light text-dark p-2">
            <i class="fas fa-clock me-1"></i> Last Updated: {{ now()->format('M j, Y g:i A') }}
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="row g-4 mb-5">
        <!-- Total Users -->
        <div class="col-xxl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="metric-icon rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                       
                    </div>
                    <h3 class="fw-bold mb-1">{{ $userCounts['total'] }}</h3>
                    <p class="text-uppercase text-muted small mb-2">Total Users</p>
                    <div class="d-flex justify-content-between small text-muted mt-3">
                        <span>Students: {{ $userCounts['students'] }}</span>
                        <span>Admins: {{ $userCounts['admins'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="col-xxl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="metric-icon rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-tasks text-success"></i>
                        </div>
                      
                    </div>
                    <h3 class="fw-bold mb-1">{{ $taskCounts['total'] }}</h3>
                    <p class="text-uppercase text-muted small mb-2">Total Tasks</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.tasks.view') }}" class="btn btn-sm btn-outline-success w-100">
                            View Details <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="col-xxl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="metric-icon rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fas fa-check-circle text-info"></i>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info p-2">
                            {{ number_format(($taskCounts['completed']/$taskCounts['total'])*100, 1) }}% Rate
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $taskCounts['completed'] }}</h3>
                    <p class="text-uppercase text-muted small mb-2">Completed Tasks</p>
                    <div class="mt-3">
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar bg-info" 
                                 style="width: {{ ($taskCounts['completed']/$taskCounts['total'])*100 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Technicians -->
        <div class="col-xxl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="metric-icon rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-tools text-warning"></i>
                        </div>
                      
                    </div>
                    <h3 class="fw-bold mb-1">{{ $userCounts['technicians'] }}</h3>
                    <p class="text-uppercase text-muted small mb-2">Active Technicians</p>
                    <div class="mt-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span class="badge rounded-circle bg-success p-2"><i class="fas fa-circle"></i></span>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <span class="small text-muted">12% from last month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row g-4">
        <!-- Recent Activities -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Recent Activities</h5>
                        <a href="{{ route('admin.tasks.view') }}" class="btn btn-sm btn-primary">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($recentTasks as $task)
                        <div class="list-group-item border-0 border-bottom px-4 py-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">Task #{{ $task->task_id }}</h6>
                                            <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }} ms-2">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </div>
                                        <small class="text-muted">{{ $task->created_at}}</small>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        {{ Str::limit($task->issue->issue_description, 80) }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'warning') }} me-2">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-user-cog me-1"></i>
                                                {{ $task->assignee->username ?? 'Unassigned' }}
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Due: {{ \Carbon\Carbon::parse($task->expected_completion)->format('M j') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Task Status Summary -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Task Status Summary</h5>
                      
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-warning mb-1">Pending Tasks</h6>
                                        <h3 class="fw-bold mb-0">{{ $taskCounts['pending'] ?? 0 }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-hourglass-half text-warning fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-primary mb-1">In Progress</h6>
                                        <h3 class="fw-bold mb-0">{{ $taskCounts['in_progress'] ?? 0 }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-spinner text-primary fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-success bg-opacity-10 rounded-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-success mb-1">Completed</h6>
                                        <h3 class="fw-bold mb-0">{{ $taskCounts['completed'] ?? 0 }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    
    .metric-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        min-width: 20px;
        border-radius: 10px;
    }
    
    .avatar {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
    }
    
    .list-group-item:hover {
        background-color: rgba(0,0,0,0.01);
    }
    
    .card {
        border-radius: 12px;
    }
    
    .card-header {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
    }
    
    .btn {
        border-radius: 8px;
    }
    
    .badge {
        border-radius: 6px;
    }
</style>
@endsection

@section('scripts')

@endsection
@endsection
