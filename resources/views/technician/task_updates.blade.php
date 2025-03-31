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
                        <span class="badge 
                            @if ($task->issue_status == 'Pending') bg-secondary
                            @elseif ($task->issue_status == 'In Progress') bg-primary
                            @elseif ($task->issue_status == 'Completed') bg-success
                            @endif">
                            {{ $task->issue_status }}
                        </span>
                    </p>
                    <p><strong>Assigned On:</strong> {{ $task->issue->report_date ?? 'N/A'}}</p>
                    <p><strong>Completed On:</strong> 
                        @if($task->issue_status == 'Completed')
                        {{$task->expected_completion->format('M d, Y H:i')}}
                        @else
                            <span class="text-muted">Not completed yet</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Update (Only if task is not completed) -->
    @if($task->issue_status != 'Completed')
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-plus-circle me-2"></i>Add New Update
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('task.add_update', $task->task_id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="update_description" class="form-label">Update Description</label>
                    <textarea class="form-control" id="update_description" name="update_description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="status_change" class="form-label">Status Change</label>
                    <select class="form-select" id="status_change" name="status_change" required>
                        <option value="" selected disabled>Select new status</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Mark as Completed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Submit Update
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>This task has been completed and cannot be modified further.
    </div>
    @endif

    <!-- Task Updates History -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Task Updates History
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
                            <p class="mb-1">{{ $update->update_description }}</p>
                            @if($update->status_change)
                            <small class="text-muted d-block">
                                Status changed to: 
                                <span class="badge 
                                    @if ($update->status_change == 'Pending') bg-secondary
                                    @elseif ($update->status_change == 'In Progress') bg-primary
                                    @elseif ($update->status_change == 'Completed') bg-success
                                    @endif">
                                    {{ $update->status_change }}
                                </span>
                            </small>
                            @endif
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

@if($task->issue_status == 'Completed')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Disable all form elements if task is completed
        const formElements = document.querySelectorAll('input, textarea, select, button');
        formElements.forEach(element => {
            element.disabled = true;
        });
    });
</script>
@endif
@endsection