@extends('Layouts.AdminNavBar')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0">Add New Location</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.locations.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Building Name*</label>
                        <input type="text" name="building_name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Floor Number*</label>
                        <input type="text" name="floor_number" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Room Number*</label>
                        <input type="text" name="room_number" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection