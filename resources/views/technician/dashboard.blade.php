@extends('layouts.TechnicianNavBar')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
      
    <!-- Welcome & Stats Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card bg-white p-4 rounded-3 shadow-sm">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div>
                        <h1 class="h3 fw-bold mb-2">Welcome back, {{ Auth::user()->username }}!</h1>
                        <p class="text-muted mb-0">Here's what's happening with your tasks today</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <span class="badge bg-light text-dark fs-6">
                            <i class="fas fa-calendar-day me-2"></i>
                            {{ now()->format('l, F j, Y') }}
                        </span>
                    </div>
                </div>

                <!-- Overdue Tasks Alert -->
                @if($overdueCount > 0)
                <div class="alert alert-danger mt-3 d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <h5 class="alert-heading mb-1">You have {{ $overdueCount }} overdue task(s)!</h5>
                        <p class="mb-0">Please prioritize these tasks immediately.</p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>
        </div>
    </div>



    <!-- Task Overview Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-clipboard-list me-2 text-primary"></i>Your Active Tasks
                        </h5>

                    </div>
                </div>
                <div class="card-body">
                    @if($tasks->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="fw-bold">No active tasks</h5>
                            <p class="text-muted">You have no pending or in-progress tasks currently assigned.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Task ID</th>
                                        <th>Description</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tasks as $task)
                                        @if($task->issue_status != 'Completed')
                                        <tr class="@if($task->expected_completion->isPast()) table-danger @endif">
                                            <td class="fw-bold">#{{ $task->task_id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="task-icon bg-light-primary rounded-circle p-2 me-3">
                                                            <i class="fas fa-tools text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ Str::limit($task->issue->issue_title, 30) }}</h6>
                                                        <small class="text-muted">{{ Str::limit($task->issue->issue_description, 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge
                                                    @if ($task->priority == 'Low') bg-success
                                                    @elseif ($task->priority == 'Medium') bg-warning
                                                    @elseif ($task->priority == 'High') bg-danger
                                                    @endif">
                                                    <i class="fas fa-flag me-1"></i>{{ $task->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge
                                                    @if ($task->issue_status == 'Pending') bg-secondary
                                                    @elseif ($task->issue_status == 'In Progress') bg-primary
                                                    @endif">
                                                    {{ $task->issue_status }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <small class="text-muted">Due</small>
                                                    <span class="fw-bold @if($task->expected_completion->isPast()) text-danger @endif">
                                                        {{ $task->expected_completion->format('M d, Y') }}
                                                        @if($task->expected_completion->isPast())
                                                        <span class="badge bg-danger ms-2">Overdue</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('technician.task_details', $task->task_id) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styles */
    .welcome-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .stat-card {
        color: white;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-card.pending {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    .stat-card.progress {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }

    .stat-card.completed {
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .task-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
    }

    .table td {
        vertical-align: middle;
    }

    .bg-white-20 {
        background-color: rgba(255, 255, 255, 0.2);
    }

    .table-danger {
        background-color: rgba(220, 53, 69, 0.05);
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
