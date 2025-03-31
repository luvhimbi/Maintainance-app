@extends('Layouts.AdminNavBar')

@section('content')
<div class="container task-view-container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 task-header text-primary">
            <i class="fas fa-tasks me-2"></i>Task Management
        </h1>
       
    </div>
    
    <!-- Information Alert -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i> If the system shows "N/A" for Admin, it means the task was automatically assigned based on technician availability.
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Task ID</th>
                            <th>Issue</th>
                            <th>Technician</th>
                            <th>Admin</th>
                            <th>Assigned</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task as $tasks)
                            <tr class="@if($tasks->issue_status == 'Completed') bg-light @endif">
                                <td class="ps-4 fw-bold">#{{ $tasks->task_id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fas fa-bug text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">ISSUE-{{ $tasks->issue_id }}</div>
                                            <small class="text-muted">@if($tasks->issue){{ Str::limit($tasks->issue->title, 30) }}@else N/A @endif</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($tasks->assignee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light-primary rounded-circle">
                                                    {{ strtoupper(substr($tasks->assignee->username, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>{{ $tasks->assignee->username }}</div>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-user-slash me-1"></i> Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($tasks->admin)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light-info rounded-circle">
                                                    {{ strtoupper(substr($tasks->admin->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>{{ $tasks->admin->name }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ $tasks->assignment_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="@if($tasks->expected_completion->isPast() && $tasks->issue_status != 'Completed') text-danger @endif">
                                        {{ $tasks->expected_completion->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill 
                                        @if($tasks->issue_status == 'Completed') bg-success
                                        @elseif($tasks->issue_status == 'In Progress') bg-primary
                                        @elseif($tasks->issue_status == 'Pending') bg-warning
                                        @else bg-secondary @endif">
                                        {{ $tasks->issue_status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill 
                                        @if($tasks->priority == 'High') bg-danger
                                        @elseif($tasks->priority == 'Medium') bg-warning
                                        @elseif($tasks->priority == 'Low') bg-success
                                        @else bg-secondary @endif">
                                        <i class="fas fa-flag me-1"></i>{{ $tasks->priority }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <a 
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="View Progress">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(!$tasks->assignee)
                                            <a href="{{ route('tasks.assign', $tasks->task_id) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               data-bs-toggle="tooltip"
                                               title="Assign Task">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                        @endif
                                        
                                       
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

<style>
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-title {
        font-weight: 600;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }
    .task-header {
        font-weight: 600;
        letter-spacing: -0.5px;
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection