@extends('Layouts.TechnicianNavbar')

@section('title', 'Update Task')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-2 mb-md-0">Update Task #{{ $task->task_id }}</h1>
        <a href="{{ route('technician.task_details', ['task_id' => $task->task_id]) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Task Details
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <form action="{{ route('tasks.update', $task->task_id) }}" method="POST">
                @csrf
                @method('PUT') 
                
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Pending" {{ old('status', $task->issue_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ old('status', $task->issue_status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ old('status', $task->issue_status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="update_description" class="form-label">Update Description</label>
                    <textarea name="update_description" id="update_description" class="form-control @error('update_description') is-invalid @enderror" rows="5" placeholder="Describe the update..." required>{{ old('update_description') }}</textarea>
                    @error('update_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Update
                </button>
            </form>
        </div>
    </div>
</div>
@endsection