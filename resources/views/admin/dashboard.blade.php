@extends('layouts.AdminNavBar')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container px-4">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center py-4">
        <div>
            <h1 class="fw-light mb-0">Dashboard Overview</h1>
            <p class="text-muted small mb-0">System statistics and key metrics</p>
        </div>
        <div class="text-muted small">
            Last Updated: {{ now()->format('M j, Y g:i A') }}
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="row g-4 mb-4">
        <!-- Metric Cards -->
        <div class="col-xxl-3 col-md-6">
            <div class="metric-card bg-white p-4 rounded-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small mb-2">Total Users</h6>
                        <h2 class="mb-0">{{ $userCounts['total'] }}</h2>
                    </div>
                    <div class="metric-icon bg-primary-light">
                        <i class="fas fa-users fa-lg text-primary"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between small">
                        <span>Students: {{ $userCounts['students'] }}</span>
                        <span>Admins: {{ $userCounts['admins'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="metric-card bg-white p-4 rounded-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small mb-2">Total Tasks</h6>
                        <h2 class="mb-0">{{ $taskCounts['total'] }}</h2>
                    </div>
                    <div class="metric-icon bg-success-light">
                        <i class="fas fa-tasks fa-lg text-success"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.tasks.view') }}" class="text-decoration-none small">
                        View Details <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="metric-card bg-white p-4 rounded-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small mb-2">Completed Tasks</h6>
                        <h2 class="mb-0">{{ $taskCounts['completed'] }}</h2>
                    </div>
                    <div class="metric-icon bg-info-light">
                        <i class="fas fa-check-circle fa-lg text-info"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" 
                             style="width: {{ ($taskCounts['completed']/$taskCounts['total'])*100 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="metric-card bg-white p-4 rounded-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small mb-2">Active Technicians</h6>
                        <h2 class="mb-0">{{ $userCounts['technicians'] }}</h2>
                    </div>
                    <div class="metric-icon bg-warning-light">
                        <i class="fas fa-tools fa-lg text-warning"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-success">↑ 12% from last month</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Visualization Section -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">Task Status Overview</h6>
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">User Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Sections -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Recent Activities</h6>
                        <a href="{{ route('admin.tasks.view') }}" class="btn btn-sm btn-outline-secondary">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recentTasks as $task)
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="d-flex align-items-start">
                              
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <div>
                                            
                                            <span class="badge bg-primary ms-2">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </div>
                                        <small >{{ $task->created_at }}</small>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        {{ Str::limit($task->issue->issue_description, 80) }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-{{ $task->status_class }} me-2">
                                                {{ ucfirst($task->status) }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-user-cog me-1"></i>
                                                {{ $task->assignee->username ?? 'Unassigned' }}
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Due: {{ $task->expected_completion}}
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
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">Top Performers</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Technician</th>
                                    <th class="text-end">Completed</th>
                                    <th class="text-end">Total Tasks</th>
                                    <th class="text-end">Efficiency</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topTechnicians as $tech)
                                @php
                                    $efficiency = $tech->total_tasks > 0 
                                        ? ($tech->completed_tasks / $tech->total_tasks) * 100 
                                        : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-light-primary rounded-circle me-2">
                                                {{ substr($tech->username, 0, 1) }}
                                            </div>
                                            <span>{{ $tech->username }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ $tech->completed_tasks }}</td>
                                    <td class="text-end">{{ $tech->total_tasks }}</td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px; width: 100px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: {{ $efficiency }}%"
                                                     aria-valuenow="{{ $efficiency }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="text-nowrap">{{ number_format($efficiency, 1) }}%</span>
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

@section('styles')
<style>
    .metric-card {
        transition: transform 0.2s;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .metric-card:hover {
        transform: translateY(-2px);
    } .progress-bar {
        min-width: 20px; /* Ensure progress is always visible */
    }
    .badge.bg-high { background-color: #dc3545; }
    .badge.bg-medium { background-color: #ffc107; }
    .badge.bg-low { background-color: #198754; }
    
    .metric-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
    }
    
    .bg-primary-light { background-color: rgba(13,110,253,0.1) }
    .bg-success-light { background-color: rgba(25,135,84,0.1) }
    .bg-info-light { background-color: rgba(13,202,240,0.1) }
    .bg-warning-light { background-color: rgba(255,193,7,0.1) }
</style>
@endsection

@section('scripts')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
    };

    // User Distribution Chart
    const userCtx = document.getElementById('userDistributionChart');
    if (userCtx) {
        new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Technicians', 'Admins'],
                datasets: [{
                    data: [{{ $userCounts['students'] ?? 0 }}, {{ $userCounts['technicians'] ?? 0 }}, {{ $userCounts['admins'] ?? 0 }}],
                    backgroundColor: ['#0d6efd', '#0dcaf0', '#6f42c1'],
                    borderWidth: 0
                }]
            },
            options: chartOptions
        });
    }

    // Task Status Chart
    const taskCtx = document.getElementById('taskStatusChart');
    if (taskCtx) {
        new Chart(taskCtx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    label: 'Tasks',
                    data: [{{ $taskCounts['pending'] ?? 0 }}, {{ $taskCounts['in_progress'] ?? 0 }}, {{ $taskCounts['completed'] ?? 0 }}],
                    backgroundColor: ['#ffc107', '#0d6efd', '#198754']
                }]
            },
            options: chartOptions
        });
    }
});


</script>
@endsection
@endsection