@extends('Layouts.AdminNavBar')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0">Edit Location</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.locations.update', $location->location_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Building Name*</label>
                        <input type="text" name="building_name" class="form-control" 
                               value="{{ old('building_name', $location->building_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Floor Number*</label>
                        <input type="text" name="floor_number" class="form-control" 
                               value="{{ old('floor_number', $location->floor_number) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Room Number*</label>
                        <input type="text" name="room_number" class="form-control" 
                               value="{{ old('room_number', $location->room_number) }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $location->description) }}</textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Location</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection