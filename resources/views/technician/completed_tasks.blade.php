@extends('Layouts.TechnicianNavBar')

@section('title', 'Completed Tasks')

@section('content')
<div class="container mt-4">
    <!-- Page Header with Stats -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold text-primary">Completed Tasks</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('technician.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Completed</li>
                </ol>
            </nav>
        </div>
        <div class="bg-light p-3 rounded">
            <span class="fw-bold">{{ $completedTasks->count() }}</span> completed tasks
        </div>
    </div>

    <!-- Completed Tasks Grid -->
    <div class="card border-0 shadow">
        <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2 text-success"></i>Recently Completed
                </h5>
                
            </div>
        </div>
        
        <div class="card-body p-4">
            @if ($completedTasks->count() > 0)
                <div class="row g-4">
                    @foreach ($completedTasks as $task)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-header bg-light-success bg-opacity-10 border-bottom-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-success rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i>Completed
                                        </span>
                                        <span class="text-muted small">#{{ $task->task_id }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-truncate">{{ $task->issue->issue_type }} Issue</h5>
                                    <p class="card-text text-muted mb-3">
                                        <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                        {{ $task->issue->location->building_name ?? 'N/A' }}, Room {{ $task->issue->location->room_number ?? 'N/A' }}
                                    </p>
                                    
                                    <div class="mb-3">
                                        <p class="card-text line-clamp-2">{{ $task->issue->issue_description }}</p>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block small text-muted">Priority</span>
                                            <span class="badge 
                                                @if($task->priority == 'High') bg-danger
                                                @elseif($task->priority == 'Medium') bg-warning
                                                @else bg-success
                                                @endif">
                                                {{ $task->priority }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="d-block small text-muted">Duration</span>
                                            <span class="fw-bold">
                                                {{ $task->assignment_date->diffInDays($task->expected_completion) }} days
                                            </span>
                                        </div>
                                        <div>
                                            <span class="d-block small text-muted">Completed</span>
                                            <span class="fw-bold">
                                                {{ $task->expected_completion->format('M d') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0 pt-0">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('tasks.updates', $task->task_id) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-history me-1"></i> View Activity Log
                                        </a>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
               
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-4x text-light" style="color: #e9ecef;"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Completed Tasks Yet</h4>
                    <p class="text-muted">Tasks you mark as completed will appear here</p>
                    <a href="{{ route('technician.dashboard') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-tasks me-1"></i> View Active Tasks
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }
    .transition-all {
        transition: all 0.2s ease;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .bg-light-success {
        background-color: rgba(25, 135, 84, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection