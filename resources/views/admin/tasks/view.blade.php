@extends('Layouts.AdminNavBar')
@section('title','View All Tasks')
@section('content')
<div class="container task-management-container py-4">
    <!-- Header Section -->
    <div class="header-section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-1">Task Management</h2>
                <p class="page-subtitle text-muted">Manage and monitor all technician tasks</p>
            </div>
            <div class="overdue-alert badge bg-danger bg-soft-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                Overdue Tasks: {{ $overdueCount }}
            </div>
        </div>
    </div>

    <!-- Task Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="table-light">
                            <th class="ps-4">ID</th>
                            <th>Issue Details</th>
                            <th>Technician</th>
                            <th>Assigned On</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr class="@if($task->issue_status == 'Completed') table-success-light 
                                      @elseif($task->expected_completion->isPast() && $task->issue_status != 'Completed') table-danger-light @endif">
                                <!-- Task ID -->
                                <td class="ps-4 fw-semibold text-muted">#{{ $task->task_id }}</td>
                                
                                <!-- Issue Details -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container text-danger me-2">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">ISSUE-{{ $task->issue_id }}</div>
                                            <small class="text-muted">
                                                @if($task->issue)
                                                    {{ Str::limit($task->issue->title, 25) }}
                                                @else 
                                                    N/A
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Technician -->
                                <td>
                                    @if($task->assignee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-light-primary me-2">
                                                {{ strtoupper(substr($task->assignee->username, 0, 1)) }}
                                            </div>
                                            <span>{{ $task->assignee->username }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-secondary">
                                            <i class="fas fa-user-times me-1"></i> Unassigned
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Assignment Date -->
                                <td>
                                    <div class="text-muted small">
                                        {{ $task->assignment_date->format('d M Y') }}
                                    </div>
                                </td>
                                
                                <!-- Due Date with Overdue Warning -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="@if($task->expected_completion->isPast() && $task->issue_status != 'Completed') text-danger @else text-muted @endif">
                                            {{ $task->expected_completion->format('d M Y') }}
                                        </div>
                                        @if($task->expected_completion->isPast() && $task->issue_status != 'Completed')
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-clock me-1"></i>Overdue
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Status -->
                                <td>
                                    @php
                                        $statusClasses = [
                                            'Completed' => 'success',
                                            'In Progress' => 'primary',
                                            'Pending' => 'warning'
                                        ];
                                    @endphp
                                    <span class="badge bg-soft-{{ $statusClasses[$task->issue_status] ?? 'secondary' }} text-{{ $statusClasses[$task->issue_status] ?? 'secondary' }}">
                                        {{ $task->issue_status }}
                                        @if($task->expected_completion->isPast() && $task->issue_status != 'Completed')
                                            <i class="fas fa-exclamation-triangle ms-1"></i>
                                        @endif
                                    </span>
                                </td>
                                
                                <!-- Priority -->
                                <td>
                                    @php
                                        $priorityClasses = [
                                            'High' => 'danger',
                                            'Medium' => 'warning',
                                            'Low' => 'success'
                                        ];
                                    @endphp
                                    <span class="badge bg-soft-{{ $priorityClasses[$task->priority] ?? 'secondary' }} text-{{ $priorityClasses[$task->priority] ?? 'secondary' }}">
                                        <i class="fas fa-flag me-1"></i>
                                        {{ $task->priority }}
                                    </span>
                                </td>
                                
                                <!-- Actions -->
                                <td class="pe-4 text-end">
                                    <div class="action-buttons">
                                        <a href="{{ route('tasks.progress.show', $task->task_id) }}" 
                                            class="btn btn-sm btn-soft-primary"
                                            data-bs-toggle="tooltip"
                                            title="View Progress">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
    /* Add these new styles */
    .table-danger-light {
        background-color: rgba(220, 53, 69, 0.03) !important;
        border-left: 3px solid #dc3545;
    }

    .overdue-alert {
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .table-danger-light td {
        position: relative;
    }

    .table-danger-light td:first-child::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background-color: #dc3545;
    }

    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Flash overdue rows
        const overdueRows = document.querySelectorAll('.table-danger-light');
        overdueRows.forEach(row => {
            row.style.animation = 'pulseAlert 1.5s infinite';
        });
    });

    // Add pulse animation for overdue tasks
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulseAlert {
            0% { background-color: rgba(220, 53, 69, 0.03); }
            50% { background-color: rgba(220, 53, 69, 0.08); }
            100% { background-color: rgba(220, 53, 69, 0.03); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection