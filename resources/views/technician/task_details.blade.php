@extends('Layouts.TechnicianNavbar')

@section('title', 'Task Details')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-2 mb-md-0">Task Details</h1>
        <a href="{{ route('technician.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to dashboard?
        </a>
        <a href="{{ route('tasks.update.form', $task->task_id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Update Task
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
                    <span class="me-2"><i class="fas fa-calendar-alt me-1"></i> Assigned: {{ $task->assignment_date->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-4 mb-md-0">
                    <div class="mb-4">
                        <h5 class="text-muted fs-6">Issue Description</h5>
                        <p class="card-text">{{ $task->issue->issue_description }}</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Status</h5>
                            <div>
                                @if($task->issue_status == 'Pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($task->issue_status == 'In Progress')
                                    <span class="badge bg-primary">In Progress</span>
                                @elseif($task->issue_status == 'Completed')
                                    <span class="badge bg-success">Completed</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Priority</h5>
                            <div>
                                @if($task->priority == 'Low')
                                    <span class="badge bg-success">Low</span>
                                @elseif($task->priority == 'Medium')
                                    <span class="badge bg-warning text-dark">Medium</span>
                                @elseif($task->priority == 'High')
                                    <span class="badge bg-danger">High</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Assignment Date</h5>
                            <p class="card-text">{{ $task->assignment_date }}</p>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Expected Completion</h5>
                            <p class="card-text">{{ $task->expected_completion}}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0 rounded-3 mb-3">
                        <div class="card-body">
                            <h5 class="card-title fs-6">
                                <i class="fas fa-building me-2"></i>Location Details
                            </h5>
                            @if ($task->issue->location)
                                <div class="mb-2">
                                    <p class="mb-0 fw-bold">{{ $task->issue->location->building_name }}</p>
                                    <p class="mb-0 small text-muted">Room {{ $task->issue->location->room_number }}</p>
                                </div>
                                <div class="ms-1 ps-1 border-start">
                                    <p class="mb-1 small">
                                        <i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                                        {{ $task->issue->location->location_name }}
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-secondary py-2 mb-0">
                                    <small><i class="fas fa-info-circle me-1"></i>No location specified</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="mb-3">
                <i class="fas fa-paperclip me-2"></i>Attachments
            </h5>
            @if ($task->issue->attachments->count() > 0)
                <div class="row g-2">
                    @foreach ($task->issue->attachments as $attachment)
                        @php
                            $fileExtension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);
                            $iconClass = 'fa-file';

                            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $iconClass = 'fa-file-image';
                            } elseif (in_array($fileExtension, ['pdf'])) {
                                $iconClass = 'fa-file-pdf';
                            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                $iconClass = 'fa-file-word';
                            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                $iconClass = 'fa-file-excel';
                            }
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4">
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <i class="fas {{ $iconClass }} fa-2x me-3 text-secondary"></i>
                                        <div class="text-truncate">{{ $attachment->original_name }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary" role="alert">
                    <i class="fas fa-info-circle me-2"></i>No attachments found
                </div>
            @endif
        </div>
    </div>
    <!-- Task Updates Section -->
<div class="mt-4">
    <h5 class="mb-3">
        <i class="fas fa-history me-2"></i>Task Updates
    </h5>

    @if ($task->updates->count() > 0)
        <div class="list-group">
            @foreach ($task->updates as $update)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $update->staff->username }}</strong>
                            <span class="badge bg-secondary">{{ $update->staff->user_role }}</span>
                        </div>
                        <small class="text-muted">{{ $update->update_timestamp}}</small>
                    </div>
                    <p class="mb-0">{{ $update->update_description }}</p>
                    <small class="text-muted">Status changed to: {{ $update->status_change }}</small>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-secondary">
            <i class="fas fa-info-circle me-2"></i>No updates yet
        </div>
    @endif
</div>

    <!-- Comment Section -->
    
@endsection


