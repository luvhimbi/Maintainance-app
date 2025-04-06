@extends('layouts.AdminNavBar')
@section('page_title', 'Task Progress')
@section('page_description', 'View the progress of the task assigned to a technician.')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-2 mb-md-0">Task Progress Details</h1>
        <a href="{{ route('admin.tasks.view') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Tasks
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-light py-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-tasks me-2 text-primary"></i>Task #{{ $task->task_id }}
                    </h4>
                </div>
                <div class="col-12 col-md-4 mt-2 mt-md-0 text-md-end">
                    <span class="me-2"><i class="fas fa-calendar-alt me-1"></i> Assigned: {{ $task->assignment_date }}</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-4 mb-md-0">
                    <!-- Status & Priority -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Current Status</h5>
                            <div>
                                <span class="badge bg-{{ $task->issue_status == 'Completed' ? 'success' : ($task->issue_status == 'In Progress' ? 'warning text-dark' : 'primary') }}">
                                    {{ $task->issue_status }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Priority Level</h5>
                            <div>
                                <span class="badge bg-{{ $task->priority == 'High' ? 'danger' : ($task->priority == 'Medium' ? 'warning text-dark' : 'success') }}">
                                    {{ $task->priority }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="mb-4">
                        <h5 class="text-muted fs-6">
                            <i class="fas fa-map-marker-alt me-2"></i>Location
                        </h5>
                        @if($task->issue->location)
                        <div class="card bg-light border-0 rounded-3">
                            <div class="card-body">
                                <p class="mb-1">
                                    {{ $task->issue->location->building_name ?? 'Unknown Building' }}
                                </p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-layer-group me-1"></i>
                                            Floor: {{ $task->issue->location->floor_number ?? 'N/A' }}
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">
                                            <i class="fas fa-door-open me-1"></i>
                                            Room: {{ $task->issue->location->room_number ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-secondary py-2 mb-0">
                            <small><i class="fas fa-info-circle me-1"></i>No location information available</small>
                        </div>
                        @endif
                    </div>

                    <!-- Task Description -->
                    <div class="mb-4">
                        <h5 class="text-muted fs-6">
                            <i class="fas fa-align-left me-2"></i>Description
                        </h5>
                        <div class="card bg-light border-0 rounded-3">
                            <div class="card-body">
                                <p class="mb-0">{{ $task->issue->issue_description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Personnel -->
                <div class="col-md-4">
                    <div class="card bg-light border-0 rounded-3 mb-3">
                        <div class="card-body">
                            <h5 class="card-title fs-6">
                                <i class="fas fa-user-cog me-2"></i>Assigned Technician
                            </h5>
                            @if($task->assignee)
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 40px; height: 40px; flex-shrink: 0;">
                                    {{ strtoupper(substr($task->assignee->username, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">{{ $task->assignee->username }}</p>
                                    <small class="text-muted">{{ $task->assignee->email }}</small>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-secondary py-2 mb-0">
                                <small><i class="fas fa-info-circle me-1"></i>No technician assigned</small>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="card bg-light border-0 rounded-3">
                        <div class="card-body">
                            <h5 class="card-title fs-6">
                                <i class="fas fa-user-shield me-2"></i>Supervising Admin
                            </h5>
                            @if($task->admin)
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 40px; height: 40px; flex-shrink: 0;">
                                    {{ strtoupper(substr($task->admin->username, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">{{ $task->admin->username }}</p>
                                    <small class="text-muted">{{ $task->admin->email }}</small>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-secondary py-2 mb-0">
                                <small><i class="fas fa-info-circle me-1"></i>System managed</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Progress Timeline -->
            <h5 class="mb-3">
                <i class="fas fa-history me-2"></i>Progress Updates
            </h5>
            
            @if($task->updates->count() > 0)
            <div class="timeline">
                @foreach($task->updates->sortByDesc('update_timestamp') as $update)
                <div class="timeline-item mb-4">
                    <div class="timeline-badge 
                        @if($update->status_change == 'In Progress') bg-warning
                        @elseif($update->status_change == 'Completed') bg-success
                        @else bg-primary @endif">
                        <i class="fas 
                            @if($update->status_change == 'In Progress') fa-wrench
                            @elseif($update->status_change == 'Completed') fa-check
                            @else fa-info @endif"></i>
                    </div>
                    <div class="timeline-content card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    @if($update->staff)
                                    <div class="symbol symbol-35px symbol-circle me-2">
                                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                                            {{ substr($update->staff->username, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>{{ $update->staff->username }}</strong>
                                        <span class="badge bg-secondary ms-2">Technician</span>
                                    </div>
                                    @else
                                    <span class="text-muted">System Generated Update</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $update->update_timestamp }}
                                </small>
                            </div>
                            <div class="mb-2">
                                <span class="badge 
                                    @if($update->status_change == 'In Progress') bg-warning text-dark
                                    @elseif($update->status_change == 'Completed') bg-success
                                    @else bg-primary @endif">
                                    {{ $update->status_change }}
                                </span>
                            </div>
                            <p class="mb-0">{{ $update->update_description }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="alert alert-secondary">
                <i class="fas fa-info-circle me-2"></i>No progress updates available yet
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-badge {
        position: absolute;
        left: -20px;
        top: 15px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
    }
    .timeline-content {
        position: relative;
        margin-left: 20px;
        border-left: 3px solid #dee2e6;
        padding-left: 25px;
        margin-bottom: 30px;
    }
    .timeline-item:last-child .timeline-content {
        border-left-color: transparent;
    }
    .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .symbol-35px {
        width: 35px;
        height: 35px;
    }
    .symbol-circle {
        border-radius: 50%;
    }
    .symbol-label {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        width: 100%;
        height: 100%;
    }
</style>
@endpush

@endsection