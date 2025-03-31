@extends('layouts.AdminNavBar')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Overview</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">System Statistics and Analytics</li>
    </ol>
    
    <!-- Stats Cards Row -->
    <div class="row">
        <!-- Users Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-normal">Total Users</h6>
                            <h3 class="font-weight-bold">{{ $userCounts['total'] }}</h3>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Tasks Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-normal">Total Tasks</h6>
                            <h3 class="font-weight-bold">{{ $taskCounts['total'] }}</h3>
                        </div>
                        <i class="fas fa-tasks fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.tasks.view') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-normal">Completed Tasks</h6>
                            <h3 class="font-weight-bold">{{ $taskCounts['completed'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.tasks.view', ['status' => 'completed']) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Technicians Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="font-weight-normal">Active Technicians</h6>
                            <h3 class="font-weight-bold">{{ $userCounts['technicians'] }}</h3>
                        </div>
                        <i class="fas fa-tools fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" >View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- User Distribution Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    User Distribution
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" width="100%" height="300"></canvas>
                </div>
                <div class="card-footer small text-muted">
                    Updated {{ now()->format('g:i A') }}
                </div>
            </div>
        </div>

        <!-- Task Status Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Task Status Overview
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" width="100%" height="300"></canvas>
                </div>
                <div class="card-footer small text-muted">
                    Updated {{ now()->format('g:i A') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Row -->
    <div class="row">
        <!-- Recent Tasks -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Recent Tasks
                    <a href="{{ route('admin.tasks.view') }}" class="btn btn-sm btn-primary float-end">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->issue->issue_type }}</strong><br>
                                        
                                    </td>
                                    <td>
                                        @if($task->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($task->status == 'in_progress')
                                            <span class="badge bg-info">In Progress</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->assignee->username ?? 'Unassigned' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Technicians -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-star me-1"></i>
                    Top Performing Technicians
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rank</th>
                                    <th>Technician</th>
                                    <th>Completed Tasks</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topTechnicians as $index => $tech)
                                <tr>
                                    <td>#{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light rounded">
                                                    {{ substr($tech->username, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>{{ $tech->username }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $tech->completed_tasks }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 role="progressbar" 
                                                 style="width: {{ ($tech->completed_tasks/max($taskCounts['completed'], 1))*100 }}%" 
                                                 aria-valuenow="{{ $tech->completed_tasks }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ max($taskCounts['completed'], 1) }}">

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // User Distribution Pie Chart
    const userCtx = document.getElementById('userDistributionChart');
    new Chart(userCtx, {
        type: 'doughnut',
        data: {
            labels: ['Students', 'Technicians', 'Admins'],
            datasets: [{
                data: [
                    {{ $userCounts['students'] }},
                    {{ $userCounts['technicians'] }},
                    {{ $userCounts['admins'] }}
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.9)',
                    'rgba(75, 192, 192, 0.9)',
                    'rgba(153, 102, 255, 0.9)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Task Status Bar Chart
    const taskCtx = document.getElementById('taskStatusChart');
    new Chart(taskCtx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                label: 'Number of Tasks',
                data: [
                    {{ $taskCounts['pending'] }},
                    {{ $taskCounts['in_progress'] }},
                    {{ $taskCounts['completed'] }}
                ],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
@endsection