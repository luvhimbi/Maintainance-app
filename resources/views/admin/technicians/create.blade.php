@extends('layouts.AdminNavBar')

@section('title', 'Add New Technician')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h5 mb-1">Add New Technician</h2>
                    <p class="text-muted small mb-0">Create a new technician account</p>
                </div>
                <a href="{{ route('admin.technicians.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
        
        <!-- Card Body -->
        <div class="card-body px-4">
            <form method="POST" action="{{ route('admin.technicians.store') }}">
                @csrf
                
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
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                               value="{{ old('phone_number') }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                 <div class="mb-3">
    <label for="specialization" class="form-label">Specialization</label>
    <select class="form-select @error('specialization') is-invalid @enderror" 
            id="specialization" 
            name="specialization" 
            required>
        <option value="" disabled selected>Select specialization</option>
        <option value="Electrical" {{ old('specialization', $staff->specialization ?? '') == 'Electrical' ? 'selected' : '' }}>Electrical</option>
        <option value="Plumbing" {{ old('specialization', $staff->specialization ?? '') == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
        <option value="Structural" {{ old('specialization', $staff->specialization ?? '') == 'Structural' ? 'selected' : '' }}>Structural</option>
    </select>
    @error('specialization')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                
                <div class="d-flex justify-content-end pt-2">
                    <button type="reset" class="btn btn-outline-secondary me-3">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Technician
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection