@extends('Layouts.TechnicianNavbar')

@section('title', 'Task Details')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('technician.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Task #{{ $task->task_id }}</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold mb-0">Task Details</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('technician.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('tasks.update.form', $task->task_id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Update Task
                </a>
            </div>
        </div>

        <!-- Main Task Card -->
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                            <i class="fas fa-tasks text-primary"></i>
                        </div>
                        <h2 class="h5 mb-0 text-dark">Task #{{ $task->task_id }}</h2>
                    </div>
                    <div class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i> Assigned: {{ $task->assignment_date->format('M d, Y') }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Left Column - Task Details -->
                    <div class="col-lg-8">
                        <!-- Issue Description -->
                        <div class="mb-4">
                            <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Issue Description</h3>
                            <div class="card bg-light border-0 p-3">
                                <p class="mb-0">{{ $task->issue->issue_description }}</p>
                            </div>
                        </div>

                        <!-- Status and Priority -->
                        <div class="row mb-4">
                            <!-- Status -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h3 class="h6 text-uppercase fw-bold mb-3 text-muted">Status</h3>
                                <div class="d-flex align-items-center">
                                    @if($task->issue_status == 'Pending')
                                        <span class="badge rounded-pill bg-warning text-dark py-2 px-3">
                    <i class="fas fa-clock me-1"></i> Pending
                </span>
                                    @elseif($task->issue_status == 'In Progress')
                                        <span class="badge rounded-pill bg-primary text-white py-2 px-3">
                    <i class="fas fa-spinner me-1 fa-spin"></i> In Progress
                </span>
                                    @elseif($task->issue_status == 'Completed')
                                        <span class="badge rounded-pill bg-success text-white py-2 px-3">
                    <i class="fas fa-check-circle me-1"></i> Completed
                </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary text-white py-2 px-3">
                    <i class="fas fa-question-circle me-1"></i> Unknown
                </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <h3 class="h6 text-uppercase fw-bold mb-3 text-muted">Priority</h3>
                                <div class="d-flex align-items-center">
                                    @if($task->priority == 'Low')
                                        <span class="badge rounded-pill bg-success text-white py-2 px-3">
                    <i class="fas fa-arrow-down me-1"></i> Low
                </span>
                                    @elseif($task->priority == 'Medium')
                                        <span class="badge rounded-pill bg-warning text-dark py-2 px-3">
                    <i class="fas fa-equals me-1"></i> Medium
                </span>
                                    @elseif($task->priority == 'High')
                                        <span class="badge rounded-pill bg-danger text-white py-2 px-3">
                    <i class="fas fa-arrow-up me-1"></i> High
                </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary text-white py-2 px-3">
                    <i class="fas fa-question me-1"></i> Unknown
                </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Assignment Date</h3>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-check text-muted me-2"></i>
                                    <span>{{ $task->assignment_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Expected Completion</h3>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-day text-muted me-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($task->expected_completion)->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Location -->
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                    </div>
                                    <h3 class="h6 text-uppercase text-muted fw-bold mb-0">Location Details</h3>
                                </div>

                                @if ($task->issue->location)
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-building text-muted mt-1 me-2"></i>
                                        <div>
                                            <h4 class="h6 mb-0">{{ $task->issue->location->building_name }}</h4>
                                            <small class="text-muted">Room {{ $task->issue->location->room_number }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-location-arrow text-muted mt-1 me-2"></i>
                                        <div>
                                            <p class="mb-0">{{ $task->issue->location->location_name }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-light py-2 mb-0">
                                        <i class="fas fa-info-circle me-1"></i> No location specified
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachments Section -->
                <hr class="my-4">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                            <i class="fas fa-paperclip text-primary"></i>
                        </div>
                        <h3 class="h6 text-uppercase text-muted fw-bold mb-0">Attachments</h3>
                    </div>

                    @if ($task->issue->attachments->count() > 0)
                        <div class="row g-3">
                            @foreach ($task->issue->attachments as $attachment)
                                @php
                                    $fileExtension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);
                                    $iconClass = 'fa-file';
                                    $iconColor = 'text-secondary';

                                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                        $iconClass = 'fa-file-image';
                                        $iconColor = 'text-success';
                                    } elseif (in_array($fileExtension, ['pdf'])) {
                                        $iconClass = 'fa-file-pdf';
                                        $iconColor = 'text-danger';
                                    } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                        $iconClass = 'fa-file-word';
                                        $iconColor = 'text-primary';
                                    } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                        $iconClass = 'fa-file-excel';
                                        $iconColor = 'text-success';
                                    }
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas {{ $iconClass }} fa-2x {{ $iconColor }} me-3"></i>
                                                <div>
                                                    <h4 class="h6 mb-0 text-truncate">{{ Str::limit($attachment->original_name, 20) }}</h4>
                                                    <small class="text-muted">{{ strtoupper($fileExtension) }} file</small>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary flex-grow-1">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                   download="{{ $attachment->original_name }}"
                                                   class="btn btn-sm btn-outline-success flex-grow-1">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-file-excel text-muted fs-4 mb-3"></i>
                                <h5 class="text-muted">No attachments found</h5>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Task Updates Section -->
        <div class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                    <i class="fas fa-history text-primary"></i>
                </div>
                <h2 class="h5 text-uppercase text-muted fw-bold mb-0">Task Updates</h2>
            </div>

            @if ($task->updates->count() > 0)
                <div class="timeline ps-3">
                    @foreach ($task->updates->sortByDesc('update_timestamp') as $update)
                        <div class="timeline-item position-relative pb-4">
                            <div class="timeline-badge position-absolute top-0 start-0 translate-middle rounded-circle d-flex align-items-center justify-content-center bg-white border border-3
                            @if($update->status_change == 'In Progress') border-warning
                            @elseif($update->status_change == 'Completed') border-success
                            @else border-primary @endif"
                                 style="width: 24px; height: 24px;">
                                <i class="fas fa-circle fs-6
                                @if($update->status_change == 'In Progress') text-warning
                                @elseif($update->status_change == 'Completed') text-success
                                @else text-primary @endif"></i>
                            </div>

                            <div class="timeline-content ms-5 ps-3">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                @if($update->staff)
                                                    <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3"
                                                         style="width: 40px; height: 40px; font-size: 1.1rem;">
                                                        {{ substr($update->staff->first_name, 0, 1) }}{{ substr($update->staff->last_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $update->staff->first_name }} {{ $update->staff->last_name }}</h6>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary small">{{ $update->staff->user_role }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">[Staff removed]</span>
                                                @endif
                                            </div>
                                            <span class="text-muted small">
                                            {{ \Carbon\Carbon::parse($update->update_timestamp)->format('M j, Y · g:i A') }}
                                        </span>
                                        </div>

                                        <div class="mb-3">
                                        <span class="badge rounded-pill py-2 px-3
                                            @if($update->status_change == 'In Progress') bg-warning bg-opacity-10 text-warning
                                            @elseif($update->status_change == 'Completed') bg-success bg-opacity-10 text-success
                                            @else bg-primary bg-opacity-10 text-primary @endif">
                                            <i class="fas
                                                @if($update->status_change == 'In Progress') fa-spinner fa-spin me-1
                                                @elseif($update->status_change == 'Completed') fa-check-circle me-1
                                                @else fa-info-circle me-1 @endif"></i>
                                            {{ $update->status_change }}
                                        </span>
                                        </div>

                                        <p class="mb-0">{{ $update->update_description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card border-0 bg-light">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-info-circle text-primary fs-4 mb-3"></i>
                        <h5 class="text-muted">No updates yet</h5>
                        <p class="text-muted mb-0">Task updates will appear here once available</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 12px;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-item:last-child .timeline-badge {
            top: 0;
        }
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }
    </style>

@endsection
