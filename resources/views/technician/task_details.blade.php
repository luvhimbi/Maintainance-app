@extends('Layouts.TechnicianNavbar')

@section('title', 'Task Details')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>

                <h1 class="h2 fw-bold mb-0 text-dark">Task Details</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('technician.dashboard') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('tasks.update.form', $task->task_id) }}" class="btn btn-primary px-4 py-2 rounded-pill">
                    <i class="fas fa-edit me-1"></i> Update Task
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5 rounded-4">
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-tasks text-primary fa-lg"></i>
                        </div>
                        <h2 class="h5 mb-0 text-dark fw-bold">Task #{{ $task->task_id }}</h2>
                    </div>
                    <div class="text-muted small">
                        
                        <span class="ms-3">
                <i class="fas fa-clock me-1"></i>
                Created: {{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('M d, Y H:i') : 'N/A' }}
            </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Issue Description</h3>
                            <div class="card bg-light border-0 p-4 rounded-3">
                                <p class="mb-0 text-dark">{{ $task->issue->issue_description }}</p>
                            </div>
                        </div>

                        <div class="row mb-4 g-3">
                            <div class="col-md-6">
                                <h3 class="h6 text-uppercase fw-bold mb-3 text-muted">Status</h3>
                                <div class="d-flex align-items-center">
                                    @php
                                        $statusBadgeClass = '';
                                        $statusIconClass = '';
                                        switch($task->issue_status) {
                                            case 'Open':
                                                $statusBadgeClass = 'bg-info-subtle text-info';
                                                $statusIconClass = 'fa-folder-open';
                                                break;
                                            case 'In Progress':
                                                $statusBadgeClass = 'bg-primary-subtle text-primary';
                                                $statusIconClass = 'fa-spinner fa-spin';
                                                break;
                                            case 'Completed':
                                                $statusBadgeClass = 'bg-success-subtle text-success';
                                                $statusIconClass = 'fa-check-circle';
                                                break;
                                            case 'Closed':
                                                $statusBadgeClass = 'bg-secondary-subtle text-secondary';
                                                $statusIconClass = 'fa-times-circle';
                                                break;
                                            default:
                                                $statusBadgeClass = 'bg-secondary-subtle text-secondary';
                                                $statusIconClass = 'fa-question-circle';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusBadgeClass }} py-2 px-3 fw-medium">
                                        <i class="fas {{ $statusIconClass }} me-1"></i> {{ $task->issue_status }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h3 class="h6 text-uppercase fw-bold mb-3 text-muted">Priority</h3>
                                <div class="d-flex align-items-center">
                                    @php
                                        $priorityBadgeClass = '';
                                        $priorityIconClass = '';
                                        switch($task->priority) {
                                            case 'Low':
                                                $priorityBadgeClass = 'bg-success-subtle text-success';
                                                $priorityIconClass = 'fa-arrow-down';
                                                break;
                                            case 'Medium':
                                                $priorityBadgeClass = 'bg-warning-subtle text-warning';
                                                $priorityIconClass = 'fa-equals';
                                                break;
                                            case 'High':
                                                $priorityBadgeClass = 'bg-danger-subtle text-danger';
                                                $priorityIconClass = 'fa-arrow-up';
                                                break;
                                            default:
                                                $priorityBadgeClass = 'bg-secondary-subtle text-secondary';
                                                $priorityIconClass = 'fa-question';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge rounded-pill {{ $priorityBadgeClass }} py-2 px-3 fw-medium">
                                        <i class="fas {{ $priorityIconClass }} me-1"></i> {{ $task->priority }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Issue Characteristics</h3>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 p-3 rounded-3 h-100 d-flex align-items-center justify-content-center">
                                        @if($task->issue->safety_hazard)
                                            <span class="badge bg-danger-subtle text-danger py-2 px-3 fw-medium">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Safety Hazard
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success py-2 px-3 fw-medium">
                                                <i class="fas fa-shield-alt me-1"></i> No Safety Hazard
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 p-3 rounded-3 h-100 d-flex align-items-center justify-content-center">
                                        @if($task->issue->affects_operations)
                                            <span class="badge bg-danger-subtle text-danger py-2 px-3 fw-medium">
                                                <i class="fas fa-exclamation-circle me-1"></i> Affects Operations
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success py-2 px-3 fw-medium">
                                                <i class="fas fa-check-circle me-1"></i> No Operational Impact
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 p-3 rounded-3 h-100 d-flex align-items-center justify-content-center">
                                        <span class="badge bg-info-subtle text-info py-2 px-3 fw-medium">
                                            <i class="fas fa-cubes me-1"></i> Affected Areas: {{ $task->issue->affected_areas }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($task->issue->issue_type === 'PC')
                            <div class="mb-4">
                                <h3 class="h6 text-uppercase text-muted fw-bold mb-3">PC Details</h3>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 p-3 rounded-3 h-100 d-flex align-items-center justify-content-center">
                                        <span class="badge bg-primary-subtle text-primary py-2 px-3 fw-medium">
                                            <i class="fas fa-desktop me-1"></i> PC Number: {{ $task->issue->pc_number ?? 'N/A' }}
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 p-3 rounded-3 h-100 d-flex align-items-center justify-content-center">
                                        <span class="badge bg-info-subtle text-info py-2 px-3 fw-medium">
                                            <i class="fas fa-cogs me-1"></i> PC Issue Type: {{ ucfirst($task->issue->pc_issue_type ?? 'N/A') }}
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 p-3 rounded-3 h-100 d-flex align-items-center justify-content-center">
                                            @if($task->issue->critical_work_affected)
                                                <span class="badge bg-danger-subtle text-danger py-2 px-3 fw-medium">
                                                <i class="fas fa-briefcase me-1"></i> Critical Work Affected
                                            </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success py-2 px-3 fw-medium">
                                                <i class="fas fa-briefcase me-1"></i> No Critical Work Impact
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Assignment Date</h3>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-check text-muted me-2"></i>
                                    <span class="text-dark">{{ $task->assignment_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Expected Completion</h3>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-day text-muted me-2"></i>
                                    <span class="text-dark">{{ \Carbon\Carbon::parse($task->expected_completion)->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="card bg-light border-0 h-100 rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-map-marker-alt text-primary fa-lg"></i>
                                    </div>
                                    <h3 class="h6 text-uppercase text-muted fw-bold mb-0">Location Details</h3>
                                </div>

                                @if ($task->issue->location)
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-building text-muted mt-1 me-2"></i>
                                        <div>
                                            <h4 class="h6 mb-0 text-dark fw-semibold">{{ $task->issue->location->building_name }}</h4>
                                            <small class="text-muted">Room {{ $task->issue->location->room_number }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-location-dot text-muted mt-1 me-2"></i>
                                        <div>
                                            <p class="mb-0 text-dark">{{ $task->issue->location->location_name }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-light py-2 mb-0 rounded-3">
                                        <i class="fas fa-info-circle me-1"></i> No location specified
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-5 border-light">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-paperclip text-primary fa-lg"></i>
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
                                    } elseif (in_array($fileExtension, ['mp4', 'mov', 'avi'])) { // Added video types
                                        $iconClass = 'fa-file-video';
                                        $iconColor = 'text-info';
                                    }
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border shadow-sm h-100 rounded-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="fas {{ $iconClass }} fa-2x {{ $iconColor }} me-3"></i>
                                                <div>
                                                    <h4 class="h6 mb-0 text-truncate fw-semibold">{{ Str::limit($attachment->original_name, 20) }}</h4>
                                                    <small class="text-muted">{{ strtoupper($fileExtension) }} file ({{ round($attachment->file_size / 1024, 1) }} KB)</small>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                   download="{{ $attachment->original_name }}"
                                                   class="btn btn-sm btn-outline-success flex-grow-1 rounded-pill">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card border-0 bg-light rounded-3">
                            <div class="card-body text-center py-4">
                                <i class="fas fa-folder-open text-muted fs-4 mb-3"></i>
                                <h5 class="text-muted fw-semibold">No attachments found</h5>
                                <p class="text-muted mb-0">This issue does not have any attached files.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-history text-primary fa-lg"></i>
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
                                <div class="card border-0 shadow-sm mb-3 rounded-3">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                @if($update->staff)
                                                    <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3"
                                                         style="width: 40px; height: 40px; font-size: 1.1rem;">
                                                        {{ substr($update->staff->first_name, 0, 1) }}{{ substr($update->staff->last_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark fw-semibold">{{ $update->staff->first_name }} {{ $update->staff->last_name }}</h6>
                                                        <span class="badge bg-primary-subtle text-primary small fw-medium">{{ $update->staff->user_role }}</span>
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
                                        <span class="badge rounded-pill py-2 px-3 fw-medium
                                            @if($update->status_change == 'In Progress') bg-warning-subtle text-warning
                                            @elseif($update->status_change == 'Completed') bg-success-subtle text-success
                                            @else bg-info-subtle text-info @endif">
                                            <i class="fas
                                                @if($update->status_change == 'In Progress') fa-spinner fa-spin me-1
                                                @elseif($update->status_change == 'Completed') fa-check-circle me-1
                                                @else fa-info-circle me-1 @endif"></i>
                                            {{ $update->status_change }}
                                        </span>
                                        </div>

                                        <p class="mb-0 text-dark">{{ $update->update_description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card border-0 bg-light rounded-3">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-info-circle text-primary fs-4 mb-3"></i>
                        <h5 class="text-muted fw-semibold">No updates yet</h5>
                        <p class="text-muted mb-0">Task updates will appear here once available</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>


        .card {
            border: 1px solid #e0e0e0; /* Subtle border for cards */
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05); /* Lighter shadow */
        }

        .card-header {
            background-color: #ffffff; /* White background for headers */
            color: #343a40; /* Dark text */
            border-bottom: 1px solid #e9ecef; /* Light border at the bottom */
        }

        .card-header h2, .card-header p {
            color: #343a40 !important; /* Ensure text is dark */
        }

        .h6.text-uppercase.text-muted.fw-bold {
            color: #6c757d !important; /* Ensure muted text color for section titles */
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important; /* Custom subtle primary */
        }
        .text-primary {
            color: #0d6efd !important;
        }

        .bg-success-subtle {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }
        .text-success {
            color: #28a745 !important;
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
        .text-warning {
            color: #ffc107 !important;
        }

        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
        .text-danger {
            color: #dc3545 !important;
        }

        .bg-info-subtle {
            background-color: rgba(23, 162, 184, 0.1) !important;
        }
        .text-info {
            color: #17a2b8 !important;
        }

        .bg-secondary-subtle {
            background-color: rgba(108, 117, 125, 0.1) !important;
        }
        .text-secondary {
            color: #6c757d !important;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        /* Timeline specific styles */
        .timeline {
            position: relative;
            padding-left: 20px;
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
        .timeline-item {
            position: relative;
            padding-bottom: 30px;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-badge {
            position: absolute;
            left: 0;
            top: 0;
            transform: translateX(-50%);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border: 3px solid;
            z-index: 1;
        }
        .timeline-content {
            margin-left: 30px;
            padding-left: 15px;
        }
        .timeline-item:last-child .timeline-content {
            border-left-color: transparent;
        }

        /* General card hover effect */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection
