@extends('Layouts.AdminNavBar')

@section('content')
<div class="container-fluid student-management py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h2 class="h5 mb-1">Student Management</h2>
                    <p class="text-muted small mb-0">Manage all student accounts and information</p>
                </div>
                
                <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                  
                    
                    <!-- Status Filter -->
                   
                    
                    <!-- Add New Button -->
                    <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i> New Student
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Card Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="border-top">
                            <!-- Student Info -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-light-primary text-primary me-3">
                                        {{ substr($student->username, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $student->username }}</div>
                                        <small class="text-muted">ID: {{ $student->user_id }}</small>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Contact Info -->
                            <td>
                                <div class="text-primary">{{ $student->email }}</div>
                                <small class="text-muted">{{ $student->phone_number ?? 'No phone' }}</small>
                            </td>
                            
                            <!-- Status -->
                            <td>
                                <span class="badge py-2 px-3 rounded-pill 
                                    @if($student->status == 'Active') bg-soft-success text-success
                                    @elseif($student->status == 'Inactive') bg-soft-secondary text-secondary
                                    @else bg-soft-warning text-warning @endif">
                                    <i class="fas fa-circle me-1" style="font-size: 6px; vertical-align: middle;"></i>
                                    {{ $student->status }}
                                </span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end">
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.students.edit', $student->user_id) }}" 
                                       class="btn btn-sm btn-soft-primary me-2"
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    
                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.students.destroy', $student->user_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-soft-danger"
                                                data-bs-toggle="tooltip" 
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this student?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                    <h5 class="mb-1">No Students Found</h5>
                                    <p class="text-muted small">Add your first student to get started</p>
                                    @if(request('search') || request('status'))
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        Clear filters
                                    </a>
                                    @else
                                    <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-plus me-1"></i> Add Student
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($students->hasPages())
            <div class="card-footer bg-transparent border-0 pt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} entries
                    </div>
                    <div>
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')

@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection