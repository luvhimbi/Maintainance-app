@extends('Layouts.TechnicianNavBar')

@section('title', 'Completed Tasks')

@section('content')
<div class="container mt-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold">Completed Tasks</h1>
        <p class="text-muted">View all tasks that have been marked as completed.</p>
    </div>

    <!-- Completed Tasks List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-check-circle me-2 text-success"></i>Completed Tasks
            </h5>
        </div>
        <div class="card-body">
            @if ($completedTasks->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach ($completedTasks as $task)
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
                                        <span class="badge bg-success">
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
                                        <strong>Completed On:</strong>
                                        <span>{{ $task->expected_completion->format('M d, Y H:i') }}</span>
                                    </div>

                                    <!-- View Updates Button -->
                                    <a href="{{ route('tasks.updates', $task->task_id) }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-history me-1"></i>View Updates
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary mb-0">
                    <i class="fas fa-info-circle me-2"></i>No completed tasks found.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
