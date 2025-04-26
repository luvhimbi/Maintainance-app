@extends('Layouts.AdminNavBar')
@section('title', 'Add New Student')
@section('content')
<div class="container-fluid student-create py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div>
                <h2 class="h5 mb-1">Add New Student</h2>
                <p class="text-muted small mb-0">Create a new student account</p>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body px-4">
            <form action="{{ route('admin.students.store') }}" method="POST" id="createStudentForm">
                @csrf
                
                <!-- Username & Email -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                               value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Phone & Status -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                               value="{{ old('phone_number') }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Suspended" {{ old('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Password Fields -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for success message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                timer: 3000
            });
        @endif

        // Check for validation errors
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `
                    <ul class="text-left">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonColor: '#3085d6'
            });
        @endif
    });
</script>

<style>
    .student-create {
        max-width: 800px;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .is-invalid {
        border-color: #dc3545;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .btn-outline-secondary {
        border-color: #dee2e6;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
    }
    
    .text-muted {
        font-size: 0.8125rem;
    }
</style>
@endsection