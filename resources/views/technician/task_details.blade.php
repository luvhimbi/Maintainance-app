@extends('Layouts.TechnicianNavBar')

@section('title', 'Task Details')

@section('content')
    <div class="container-fluid px-4 py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                    <i class="fas fa-tasks text-primary fs-4"></i>
                </div>
                <div>
                    <h1 class="h3 fw-bold mb-1 text-dark">Task Details</h1>
                    <p class="text-muted mb-0">Task ID: #{{ $task->task_id }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('technician.dashboard') }}" class="btn btn-outline-primary px-4 py-2 rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
                <a href="{{ route('tasks.update.form', $task->task_id) }}" class="btn btn-primary px-4 py-2 rounded-pill">
                    <i class="fas fa-edit me-2"></i> Update Task
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                                <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-2">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                                Task Information
                            </h5>
                            <span class="text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                Created: {{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('M d, Y H:i') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Issue Description</h6>
                            <div class="card bg-light border-0 p-4 rounded-3">
                                <p class="mb-0 text-dark">{{ $task->issue->issue_description }}</p>
                            </div>
                        </div>

                        <div class="row mb-4 g-3">
                            <div class="col-md-6">
                                <h6 class="text-uppercase fw-bold mb-3 text-muted">Status</h6>
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
                                <h6 class="text-uppercase fw-bold mb-3 text-muted">Priority</h6>
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
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Issue Characteristics</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-1">Safety Hazard</p>
                                                <p class="mb-0 fw-bold text-dark">
                                                    {{ $task->issue->safety_hazard ? 'Yes' : 'No' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-1">Affects Operations</p>
                                                <p class="mb-0 fw-bold text-dark">
                                                    {{ $task->issue->affects_operations ? 'Yes' : 'No' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3">
                                                <i class="fas fa-cubes"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-1">Affected Areas</p>
                                                <p class="mb-0 fw-bold text-dark">{{ $task->issue->affected_areas }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($task->issue->issue_type === 'PC')
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted fw-bold mb-3">PC Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                                    <i class="fas fa-desktop"></i>
                                                </div>
                                                <div>
                                                    <p class="small text-muted mb-1">PC Number</p>
                                                    <p class="mb-0 fw-bold text-dark">{{ $task->issue->pc_number ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3">
                                                    <i class="fas fa-cogs"></i>
                                                </div>
                                                <div>
                                                    <p class="small text-muted mb-1">PC Issue Type</p>
                                                    <p class="mb-0 fw-bold text-dark">{{ ucfirst($task->issue->pc_issue_type ?? 'N/A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                                    <i class="fas fa-briefcase"></i>
                                                </div>
                                                <div>
                                                    <p class="small text-muted mb-1">Critical Work</p>
                                                    <p class="mb-0 fw-bold text-dark">
                                                        {{ $task->issue->critical_work_affected ? 'Affected' : 'Not Affected' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <p class="small text-muted mb-1">Assignment Date</p>
                                            <p class="mb-0 fw-bold text-dark">{{ $task->assignment_date->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0 p-3 rounded-3 h-100">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3">
                                            <i class="fas fa-calendar-day"></i>
                                        </div>
                                        <div>
                                            <p class="small text-muted mb-1">Expected Completion</p>
                                            <p class="mb-0 fw-bold text-dark">{{ \Carbon\Carbon::parse($task->expected_completion)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-2">
                                <i class="fas fa-history"></i>
                            </span>
                            Task Updates History
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($task->updates->count() > 0)
                            <div class="timeline ps-3">
                                @foreach ($task->updates->sortByDesc('update_timestamp') as $update)
                                    <div class="timeline-item position-relative pb-4">
                                        <div class="timeline-badge position-absolute top-0 start-0 translate-middle rounded-circle d-flex align-items-center justify-content-center bg-white border border-3
                                            @if($update->status_change == 'In Progress') border-primary
                                            @elseif($update->status_change == 'Completed') border-success
                                            @else border-info @endif"
                                             style="width: 24px; height: 24px;">
                                            <i class="fas fa-circle fs-6
                                                @if($update->status_change == 'In Progress') text-primary
                                                @elseif($update->status_change == 'Completed') text-success
                                                @else text-info @endif"></i>
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
                                                                    <span class="badge bg-secondary-subtle text-secondary small fw-medium">{{ $update->staff->user_role }}</span>
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
                                                            @if($update->status_change == 'In Progress') bg-primary-subtle text-primary
                                                            @elseif($update->status_change == 'Completed') bg-success-subtle text-success
                                                            @else bg-info-subtle text-info @endif">
                                                            <i class="fas
                                                                @if($update->status_change == 'In Progress') fa-spinner fa-spin me-1
                                                                @elseif($update->status_change == 'Completed') fa-check-circle me-1
                                                                @else fa-info-circle me-1 @endif"></i>
                                                            Status: {{ $update->status_change }}
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
                                <div class="card-body text-center py-5">
                                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-info-circle fs-4"></i>
                                    </div>
                                    <h5 class="text-muted fw-semibold">No updates yet</h5>
                                    <p class="text-muted mb-0">Task updates will appear here once available.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Location Details Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold d-flex align-items-center mb-3 text-dark">
                            <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-2">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            Location Details
                        </h5>

                        @if ($task->issue->building)
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-light p-2 rounded-3 me-3">
                                    <i class="fas fa-building text-muted"></i>
                                </div>
                                <div>
                                    <p class="small text-muted mb-1">Building</p>
                                    <p class="mb-0 fw-bold text-dark">{{ $task->issue->building->building_name }}</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-light p-2 rounded-3 me-3">
                                    <i class="fas fa-layer-group text-muted"></i>
                                </div>
                                <div>
                                    <p class="small text-muted mb-1">Floor</p>
                                    <p class="mb-0 fw-bold text-dark">Floor {{ $task->issue->floor->floor_number }}</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <div class="bg-light p-2 rounded-3 me-3">
                                    <i class="fas fa-door-open text-muted"></i>
                                </div>
                                <div>
                                    <p class="small text-muted mb-1">Room</p>
                                    <p class="mb-0 fw-bold text-dark">Room {{ $task->issue->room->room_number }}</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border d-flex align-items-center rounded-3" role="alert">
                                <i class="fas fa-info-circle text-secondary me-2"></i>
                                <div>
                                    <small class="text-muted">No location specified</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attachments Card -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold d-flex align-items-center mb-3 text-dark">
                            <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-2">
                                <i class="fas fa-paperclip"></i>
                            </span>
                            Attachments
                        </h5>

                        @if ($task->issue->attachments->count() > 0)
                            <div class="row g-3">
                                @foreach ($task->issue->attachments as $attachment)
                                    @php
                                        $fileExtension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);
                                        $iconClass = 'fa-file';
                                        $bgClass = 'bg-secondary-subtle text-secondary';

                                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            $iconClass = 'fa-file-image';
                                            $bgClass = 'bg-success-subtle text-success';
                                        } elseif (in_array($fileExtension, ['pdf'])) {
                                            $iconClass = 'fa-file-pdf';
                                            $bgClass = 'bg-danger-subtle text-danger';
                                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                            $iconClass = 'fa-file-word';
                                            $bgClass = 'bg-primary-subtle text-primary';
                                        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                            $iconClass = 'fa-file-excel';
                                            $bgClass = 'bg-success-subtle text-success';
                                        } elseif (in_array($fileExtension, ['mp4', 'mov', 'avi'])) {
                                            $iconClass = 'fa-file-video';
                                            $bgClass = 'bg-info-subtle text-info';
                                        }
                                    @endphp
                                    <div class="col-12">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="{{ $bgClass }} p-3 rounded-3 me-3">
                                                        <i class="fas {{ $iconClass }} fa-lg"></i>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="mb-0 fw-bold text-truncate text-dark">{{ $attachment->original_name }}</p>
                                                        <p class="small text-muted mb-0 text-uppercase">{{ $fileExtension }} file</p>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 mt-3">
                                                    <a href="{{ route('files.view', ['id' => $attachment->attachment_id]) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </a>
                                                    <a href="{{ route('files.download', ['id' => $attachment->attachment_id]) }}"
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
                                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                        <i class="fas fa-folder-open fs-4"></i>
                                    </div>
                                    <h5 class="text-muted fw-semibold">No attachments found</h5>
                                    <p class="text-muted mb-0">This issue does not have any attached files.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Card Styles */
        .card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 0.5em 1em;
        }

        /* Timeline Styles */
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

        /* Custom Colors */
        .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
        .bg-success-subtle { background-color: rgba(40, 167, 69, 0.1) !important; }
        .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
        .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
        .bg-info-subtle { background-color: rgba(23, 162, 184, 0.1) !important; }
        .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }

        /* Button Styles */
        .btn {
            font-weight: 500;
            padding: 0.5rem 1.5rem;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .btn-outline-primary {
            color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }

        /* Form Control Styles */
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Alert Styles */
        .alert {
            border-radius: 0.5rem;
            border: none;
        }

        /* Avatar Styles */
        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
    </style>
@endsection
