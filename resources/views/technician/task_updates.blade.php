@extends('Layouts.TechnicianNavBar')

@section('title', 'Task Updates')

@section('content')
<div class="container mt-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold">Task Updates</h1>
        <p class="text-muted">View all updates for Task #{{ $task->task_id }}.</p>
    </div>

    <!-- Task Details -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-info-circle me-2"></i>Task Details
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Task ID:</strong> {{ $task->task_id }}</p>
                    <p><strong>Issue Description:</strong> {{ $task->issue->issue_description }}</p>
                    <p><strong>Priority:</strong> 
                        <span class="badge 
                            @if ($task->priority == 'Low') bg-success
                            @elseif ($task->priority == 'Medium') bg-warning
                            @elseif ($task->priority == 'High') bg-danger
                            @endif">
                            {{ $task->priority }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> 
                        <span class="badge bg-success">
                            {{ $task->issue_status }}
                        </span>
                    </p>
                    <p><strong>Assigned On:</strong> {{ $task->assignment_date}}</p>
                    <p><strong>Completed On:</strong> {{ $task->expected_completion }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Updates -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Task Updates
            </h5>
        </div>
        <div class="card-body">
            @if ($task->updates->count() > 0)
                <div class="list-group">
                    @foreach ($task->updates as $update)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $update->staff->username }}</strong>
                                    <span class="badge bg-secondary">{{ $update->staff->user_role }}</span>
                                </div>
                                <small class="text-muted">{{ $update->update_timestamp }}</small>
                            </div>
                            <p class="mb-0">{{ $update->update_description }}</p>
                            <small class="text-muted">Status changed to: {{ $update->status_change }}</small>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary mb-0">
                    <i class="fas fa-info-circle me-2"></i>No updates found for this task.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection