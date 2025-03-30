@extends('Layouts.AdminNavBar')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0">Edit Student</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.students.update', $student->user_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Username*</label>
                        <input type="text" name="username" class="form-control" 
                               value="{{ old('username', $student->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email*</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email', $student->email) }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" 
                               value="{{ old('phone_number', $student->phone_number) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status*</label>
                        <select name="status" class="form-select" required>
                            <option value="Active" {{ old('status', $student->status) == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status', $student->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Suspended" {{ old('status', $student->status) == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection