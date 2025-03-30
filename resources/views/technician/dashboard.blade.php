@extends('layouts.TechnicianNavBar')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <!-- Welcome Message -->
    <div class="mb-4">
        <h1 class="h3 fw-bold">Welcome, {{ Auth::user()->username }}!</h1>
        <p class="text-muted">You have {{ $pendingTasks }} pending tasks, {{ $inProgressTasks }} in progress, and {{ $completedTasks }} completed.</p>
    </div>

    <!-- Task Overview -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-tasks me-2"></i>Assigned Tasks
            </h5>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($tasks as $task)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Task #{{ $task->task_id }}</h5>
                                <p class="card-text">{{ Str::limit($task->issue->issue_description, 100) }}</p>

                                <!-- Priority -->
                                <div class="mb-3">
                                    <strong>Priority:</strong>
                                    <span class="badge 
                                        @if ($task->priority == 'Low') bg-success
                                        @elseif ($task->priority == 'Medium') bg-warning
                                        @elseif ($task->priority == 'High') bg-danger
                                        @endif">
                                        {{ $task->priority }}
                                    </span>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <strong>Status:</strong>
                                    <span class="badge 
                                        @if ($task->issue_status == 'Pending') bg-secondary
                                        @elseif ($task->issue_status == 'In Progress') bg-primary
                                        @elseif ($task->issue_status == 'Completed') bg-success
                                        @endif">
                                        {{ $task->issue_status }}
                                    </span>
                                </div>

                                <!-- Assignment Date -->
                                <div class="mb-3">
                                    <strong>Assigned On:</strong>
                                    <span>{{ $task->assignment_date->format('M d, Y H:i') }}</span>
                                </div>

                                <!-- Expected Completion -->
                                <div class="mb-3">
                                    <strong>Expected Completion:</strong>
                                    <span>{{ $task->expected_completion->format('M d, Y H:i') }}</span>
                                </div>

                                <!-- View Details Button -->
                                <a href="{{ route('technician.task_details', $task->task_id) }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection