@extends('Layouts.AdminNavBar')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pb-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <h2 class="h5 mb-3 mb-md-0 fw-semibold text-primary">
                    <i class="fas fa-user-graduate me-2"></i> Student Management
                </h2>
                
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <form action="{{ route('admin.students.index') }}" method="GET" class="flex-grow-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                   placeholder="Search students..." value="{{ request('search') }}">
                        </div>
                    </form>
                    
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.students.index') }}" 
                           class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                            All
                        </a>
                        @foreach(['Active', 'Inactive', 'Suspended'] as $status)
                        <a href="{{ route('admin.students.index', ['status' => $status]) }}" 
                           class="btn {{ request('status') == $status ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $status }}
                        </a>
                        @endforeach
                    </div>
                    
                    <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i> New Student
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body px-0 py-2">
            <div class="table-responsive rounded-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="border-top">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px symbol-circle me-3">
                                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                                            {{ substr($student->username, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $student->username }}</div>
                                        <small class="text-muted">ID: {{ $student->user_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-primary">{{ $student->email }}</div>
                                <small class="text-muted">{{ $student->phone_number ?? 'No phone' }}</small>
                            </td>
                            <td>
                                <span class="badge py-2 px-3 fs-8 fw-normal rounded-pill 
                                    @if($student->status == 'Active') bg-success-light text-success
                                    @elseif($student->status == 'Inactive') bg-secondary-light text-secondary
                                    @else bg-warning-light text-warning @endif">
                                    <i class="fas fa-circle me-1" style="font-size: 6px; vertical-align: middle;"></i>
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.students.edit', $student->user_id) }}" 
                                       class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-2"
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.students.destroy', $student->user_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                onclick="return confirm('Are you sure you want to delete this student?')"
                                                data-bs-toggle="tooltip" title="Delete">
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
                                   
                                    <h4 class="text-muted mb-3">No students found</h4>
                                    @if(request('search') || request('status'))
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary">
                                        Clear filters
                                    </a>
                                    @else
                                    <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i> Add your first student
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
            <div class="card-footer bg-transparent border-0 pt-0">
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
@endsection

@push('styles')
<style>
    .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .symbol-40px {
        width: 40px;
        height: 40px;
    }
    .symbol-circle {
        border-radius: 50%;
    }
    .symbol-label {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        width: 100%;
        height: 100%;
    }
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .bg-secondary-light {
        background-color: rgba(108, 117, 125, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .empty-state {
        max-width: 400px;
        margin: 0 auto;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    // Enable Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush