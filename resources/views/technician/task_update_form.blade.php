@extends('Layouts.TechnicianNavbar')

@section('title', 'Update Task')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-2 mb-md-0">Update Task #{{ $task->task_id }}</h1>
        <a href="{{ route('Assigned_tasks', $task->task_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Task Details
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ route('tasks.update', $task->task_id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="Pending" {{ $task->issue_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ $task->issue_status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ $task->issue_status == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="update_description" class="form-label">Update Description</label>
                    <textarea name="update_description" id="update_description" class="form-control" rows="5" placeholder="Describe the update..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Update
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
