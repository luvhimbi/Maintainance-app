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

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <!-- Total Users -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h3 class="mb-0">{{ $userCounts['total'] }}</h3>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                       <span>Students: {{ $userCounts['Students'] }}</span>
                       <span>Staff: {{ $userCounts['Staff_member'] }}</span>
                       <span>Admins: {{ $userCounts['admins'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Technicians -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-tools text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Technicians</h6>
                            <h3 class="mb-0">{{ $userCounts['technicians'] }}</h3>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Available: {{ $userCounts['available_technicians'] ?? 0 }}</span>
                        <span>Busy: {{ $userCounts['busy_technicians'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-tasks text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Tasks</h6>
                            <h3 class="mb-0">{{ $taskCounts['total'] }}</h3>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Completed: {{ $taskCounts['completed'] }}</span>
                        <span>Pending: {{ $taskCounts['pending'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-chart-line text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Completion Rate</h6>
                            <h3 class="mb-0">{{ number_format($taskCounts['completion_rate'], 1) }}%</h3>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ $taskCounts['completion_rate'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Task Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Pending Tasks -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Pending Tasks</h6>
                                    <span class="badge bg-warning">{{ $taskCounts['pending'] }}</span>
                                </div>
                                <div class="small text-muted">
                                    Tasks waiting for assignment or action
                                </div>
                            </div>
                        </div>

                        <!-- In Progress Tasks -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">In Progress</h6>
                                    <span class="badge bg-primary">{{ $taskCounts['in_progress'] }}</span>
                                </div>
                                <div class="small text-muted">
                                    Tasks currently being worked on
                                </div>
                            </div>
                        </div>

                        <!-- Completed Tasks -->
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Completed</h6>
                                    <span class="badge bg-success">{{ $taskCounts['completed'] }}</span>
                                </div>
                                <div class="small text-muted">
                                    Successfully completed tasks
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Distribution and Top Technicians -->
    <div class="row g-4">
        <!-- Priority Distribution -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Task Priority Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- High Priority -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-exclamation-circle text-danger"></i>
                                    </div>
                                    <span>High Priority</span>
                                </div>
                                <span class="badge bg-danger">{{ $priorityDistribution['high'] }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: {{ $priorityDistribution['high_percentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- Medium Priority -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-exclamation text-warning"></i>
                                    </div>
                                    <span>Medium Priority</span>
                                </div>
                                <span class="badge bg-warning">{{ $priorityDistribution['medium'] }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: {{ $priorityDistribution['medium_percentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- Low Priority -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <span>Low Priority</span>
                                </div>
                                <span class="badge bg-success">{{ $priorityDistribution['low'] }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $priorityDistribution['low_percentage'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Technicians -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Top Technicians by Completed Tasks</h5>
                </div>
                <div class="card-body">
                    @foreach($topTechnicians as $technician)
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-user-cog text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">{{ $technician->first_name }} {{ $technician->last_name }}</h6>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-3" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $technician->completion_percentage }}%"></div>
                                </div>
                                <span class="badge bg-primary">{{ $technician->completed_tasks }} Tasks</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        min-width: 20px;
        border-radius: 10px;
    }

    .badge {
        border-radius: 6px;
    }

    .btn {
        border-radius: 8px;
    }
</style>
@endsection
@endsection
