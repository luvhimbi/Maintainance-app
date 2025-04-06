@extends('Layouts.AdminNavBar')

@section('content')
<div class="container task-management-container py-4">
    <!-- Header Section -->
    <div class="header-section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-1">Task Management</h2>
                <p class="page-subtitle text-muted">Manage and monitor all technician tasks</p>
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
                            <th>Assigned By</th>
                            <th>Assigned On</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task as $tasks)
                            <tr class="@if($tasks->issue_status == 'Completed') table-success-light @endif">
                                <!-- Task ID -->
                                <td class="ps-4 fw-semibold text-muted">#{{ $tasks->task_id }}</td>
                                
                                <!-- Issue Details -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container text-danger me-2">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">ISSUE-{{ $tasks->issue_id }}</div>
                                            <small class="text-muted">
                                                @if($tasks->issue)
                                                    {{ Str::limit($tasks->issue->title, 25) }}
                                                @else 
                                                    N/A
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Technician -->
                                <td>
                                    @if($tasks->assignee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-light-primary me-2">
                                                {{ strtoupper(substr($tasks->assignee->username, 0, 1)) }}
                                            </div>
                                            <span>{{ $tasks->assignee->username }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-secondary">
                                            <i class="fas fa-user-times me-1"></i> Unassigned
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Admin -->
                               
                               
                                <td>
                                    
                                        <span class="text-muted">System</span>
                                   
                                </td>
                                
                                <!-- Assignment Date -->
                                <td>
                                    <div class="text-muted small">
                                        {{ $tasks->assignment_date->format('d M Y') }}
                                    </div>
                                </td>
                                
                                <!-- Due Date -->
                                <td>
                                    <div class="@if($tasks->expected_completion->isPast() && $tasks->issue_status != 'Completed') text-danger @else text-muted @endif">
                                        {{ $tasks->expected_completion->format('d M Y') }}
                                        @if($tasks->expected_completion->isPast() && $tasks->issue_status != 'Completed')
                                            <i class="fas fa-exclamation ms-1"></i>
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
                                    <span class="badge bg-soft-{{ $statusClasses[$tasks->issue_status] ?? 'secondary' }} text-{{ $statusClasses[$tasks->issue_status] ?? 'secondary' }}">
                                        {{ $tasks->issue_status }}
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
                                    <span class="badge bg-soft-{{ $priorityClasses[$tasks->priority] ?? 'secondary' }} text-{{ $priorityClasses[$tasks->priority] ?? 'secondary' }}">
                                        <i class="fas fa-flag me-1"></i>
                                        {{ $tasks->priority }}
                                    </span>
                                </td>
                                
                                <!-- Actions -->
                                <td class="pe-4 text-end">
                                    <div class="action-buttons">
                                        <a href="{{ route('tasks.progress.show', $tasks->task_id) }}" 
                                            class="btn btn-sm btn-soft-primary"
                                            data-bs-toggle="tooltip"
                                            title="View Progress">
                                             <i class="fas fa-eye"></i>
                                         </a>
                                        
                                        @if(!$tasks->assignee)
                                            <a href="{{ route('tasks.assign', ['task_id' => $tasks->task_id]) }}"
                                               class="btn btn-sm btn-soft-warning"
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
    /* Base Styles */
    .task-management-container {
        max-width: 1400px;
    }
    
    /* Header Styles */
    .header-section {
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
    }
    
    .page-title {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .page-subtitle {
        font-size: 0.875rem;
    }
    
    /* Table Styles */
    .table {
        margin-bottom: 0;
    }
    
    .table th {
        font-weight: 500;
        color: #6c757d;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
        border-top: 1px solid #f8f9fa;
    }
    
    /* Avatar Styles */
    .avatar-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Badge Styles */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.75rem;
    }
    
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.1);
    }
    
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    /* Completed row styling */
    .table-success-light {
        background-color: rgba(25, 135, 84, 0.03);
    }
    
    /* Action buttons */
    .action-buttons .btn {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin-left: 5px;
        border-radius: 6px;
    }
    
    .btn-soft-primary {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
        border: none;
    }
    
    .btn-soft-warning {
        color: #ffc107;
        background-color: rgba(255, 193, 7, 0.1);
        border: none;
    }
    
    /* Icon container */
    .icon-container {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Info badge */
    .info-badge .badge {
        font-weight: 400;
        font-size: 0.8rem;
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