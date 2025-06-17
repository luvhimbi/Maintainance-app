@extends('Layouts.TechnicianNavBar')


@section('title', 'Update Task')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>

                <h1 class="h2 fw-bold mb-0">Update Task #{{ $task->task_id }}</h1>
            </div>
            <a href="{{ route('technician.task_details', ['task_id' => $task->task_id]) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Task
            </a>
        </div>

        <!-- Alerts Section -->
        <div class="mb-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle flex-shrink-0 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-exclamation-triangle flex-shrink-0 me-2"></i>
                        <h5 class="mb-0">Please fix the following issues:</h5>
                    </div>
                    <ul class="mb-0 ps-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <!-- Update Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                        <i class="fas fa-edit text-primary"></i>
                    </div>
                    <h2 class="h5 mb-0 text-dark">Task Update Form</h2>
                </div>
            </div>

            <div class="card-body">
                <form id="updateTaskForm" action="{{ route('tasks.update', $task->task_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Current Status Indicator -->
                    <div class="mb-4">
                        <h3 class="h6 text-uppercase text-muted fw-bold mb-3">Current Status</h3>
                        <div class="d-flex align-items-center">
                            @if($task->issue_status == 'Pending')
                                <span class="badge rounded-pill bg-warning bg-opacity-15  py-2 px-3" style="background-color: #6c757d; color: #fff;">
                                <i class="fas fa-clock me-1"></i> Pending
                            </span>
                            @elseif($task->issue_status == 'In Progress')
                                <span class="badge rounded-pill bg-primary bg-opacity-15  py-2 px-3" style="background-color: #6c757d; color: #fff;">
                                <i class="fas fa-spinner me-1 fa-spin"></i> In Progress
                            </span>
                            @elseif($task->issue_status == 'Completed')
                                <span class="badge rounded-pill bg-success bg-opacity-15  py-2 px-3"  style="background-color: #6c757d; color: #fff;">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">Update Status</label>
                        <select name="status" id="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                            <option value="" disabled>Select new status...</option>
                            <option value="Pending" {{ old('status', $task->issue_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ old('status', $task->issue_status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ old('status', $task->issue_status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select the current status of this task</small>
                    </div>

                    <!-- Update Description -->
                    <div class="mb-4">
                        <label for="update_description" class="form-label fw-bold">Update Description</label>
                        <textarea name="update_description" id="update_description" class="form-control @error('update_description') is-invalid @enderror" rows="5" placeholder="Describe what work has been done, any issues encountered, or completion details..." required>{{ old('update_description') }}</textarea>
                        @error('update_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Provide details about your progress or completion</small>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-2"></i> Save Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .form-select-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        .invalid-feedback {
            margin-top: 0.25rem;
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateForm = document.getElementById('updateTaskForm');
        if (updateForm) {
            updateForm.addEventListener('submit', function() {
                Swal.fire({
                    title: 'Updating Task...',
                    html: '<div class="spinner-border text-primary" role="status"></div><br>Please wait while we notify the reporter.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        }
    });
    </script>
    @endpush
@endsection
