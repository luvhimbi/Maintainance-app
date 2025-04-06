@extends('layouts.AdminNavBar')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-user-cog me-2"></i>Technician Performance Report
            </h4>
        </div>
        
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.reports.technician-performance') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="Completed" {{ $statusFilter == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Pending" {{ $statusFilter == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ $statusFilter == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select name="priority" id="priority" class="form-select">
                            <option value="all" {{ $priorityFilter == 'all' ? 'selected' : '' }}>All Priorities</option>
                            <option value="High" {{ $priorityFilter == 'High' ? 'selected' : '' }}>High</option>
                            <option value="Medium" {{ $priorityFilter == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="Low" {{ $priorityFilter == 'Low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="technician_id" class="form-label">Technician</label>
                        <select name="technician_id" id="technician_id" class="form-select">
                            <option value="all" {{ $technicianId == 'all' ? 'selected' : '' }}>All Technicians</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $technicianId == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Export Buttons -->
            <div class="d-flex justify-content-end mb-4">
                <a 
                   class="btn btn-danger me-2">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
                <a 
                   class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>
            </div>
            
            <!-- Performance Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Technician</th>
                            <th>Total Tasks</th>
                            <th>Completed Tasks</th>
                            <th>Completion Rate</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($technicians as $technician)
                            @php
                                $completionRate = $technician->total_tasks > 0 
                                    ? round(($technician->completed_tasks / $technician->total_tasks) * 100, 2)
                                    : 0;
                            @endphp
                            <tr>
                                <td>{{ $technician->username }}</td>
                                <td>{{ $technician->total_tasks }}</td>
                                <td>{{ $technician->completed_tasks }}</td>
                                <td>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar {{ $completionRate >= 80 ? 'bg-success' : ($completionRate >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ $completionRate }}%" 
                                             aria-valuenow="{{ $completionRate }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $completionRate }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($completionRate >= 80)
                                        <span class="badge bg-success">Excellent</span>
                                    @elseif($completionRate >= 50)
                                        <span class="badge bg-warning text-dark">Average</span>
                                    @else
                                        <span class="badge bg-danger">Needs Improvement</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No technicians found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Performance Summary -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Performance Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Top Performers</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($technicians->sortByDesc(function($tech) { 
                                    return $tech->total_tasks > 0 ? ($tech->completed_tasks / $tech->total_tasks) : 0; 
                                })->take(3) as $topTech)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $topTech->username }}</span>
                                        <span class="badge bg-primary rounded-pill">
                                            {{ $topTech->total_tasks > 0 ? round(($topTech->completed_tasks / $topTech->total_tasks) * 100, 2) : 0 }}%
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Performance Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($technicians->pluck('username')) !!},
                datasets: [{
                    label: 'Completion Rate (%)',
                    data: {!! json_encode($technicians->map(function($tech) { 
                        return $tech->total_tasks > 0 ? round(($tech->completed_tasks / $tech->total_tasks) * 100, 2) : 0; 
                    })) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Completion Rate (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Technicians'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 0.25rem;
    }
    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .card-header {
        font-weight: 600;
    }
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
</style>
@endpush