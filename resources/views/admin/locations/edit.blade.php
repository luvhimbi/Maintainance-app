@extends('Layouts.AdminNavBar')
@section('title', 'Edit Location')
@section('content')
<div class="container-fluid location-edit py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div>
                <h2 class="h5 mb-1">Edit Location</h2>
                <p class="text-muted small mb-0">Update the details for this location</p>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body px-4">
            <form action="{{ route('admin.locations.update', $location->location_id) }}" method="POST" id="editLocationForm">
                @csrf
                @method('PUT')
                <div class="row mb-4">
                    <!-- Building Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Building Name <span class="text-danger">*</span></label>
                        <input type="text" name="building_name" class="form-control @error('building_name') is-invalid @enderror" 
                               value="{{ old('building_name', $location->building_name) }}" required>
                        @error('building_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Floor Number -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Floor Number <span class="text-danger">*</span></label>
                        <input type="text" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror" 
                               value="{{ old('floor_number', $location->floor_number) }}" required>
                        @error('floor_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Room Number -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Room Number <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" 
                               value="{{ old('room_number', $location->room_number) }}" required>
                        @error('room_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description', $location->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary me-3">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Location
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

        // Form submission handling
        const form = document.getElementById('editLocationForm');
        form.addEventListener('submit', function(e) {
            // Client-side validation can be added here if needed
        });
    });
</script>

<style>
    .location-edit {
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
</style>
@endsection