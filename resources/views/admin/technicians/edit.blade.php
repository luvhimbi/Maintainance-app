@extends('layouts.AdminNavBar')

@section('title', 'Edit Technician')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h5 mb-1">Edit Technician</h2>
                    <p class="text-muted small mb-0">Update technician information</p>
                </div>
                <a href="{{ route('admin.technicians.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
        
        <div class="card-body px-4">
            <form method="POST" action="{{ route('admin.technicians.update', $technician->user_id) }}">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" 
                               class="form-control @error('first_name') is-invalid @enderror" 
                               value="{{ old('first_name', $technician->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" 
                               class="form-control @error('last_name') is-invalid @enderror" 
                               value="{{ old('last_name', $technician->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" 
                               class="form-control @error('address') is-invalid @enderror" 
                               value="{{ old('address', $technician->address) }}" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $technician->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" 
                               class="form-control @error('phone_number') is-invalid @enderror" 
                               value="{{ old('phone_number', $technician->phone_number) }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Specialization <span class="text-danger">*</span></label>
                        <select name="specialization" class="form-select @error('specialization') is-invalid @enderror" required>
                            <option value="">Select Specialization</option>
                            @foreach($specializations as $specialization)
                            <option value="{{ $specialization }}" 
                                {{ old('specialization', $technician->maintenanceStaff->specialization ?? '') == $specialization ? 'selected' : '' }}>
                                {{ $specialization }}
                            </option>
                            @endforeach
                        </select>
                        @error('specialization')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.technicians.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Technician
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection