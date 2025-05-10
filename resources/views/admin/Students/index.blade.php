@extends('Layouts.AdminNavBar')
@section('title', 'Manage Students')
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
                            <th>Address</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="border-top">
                            <!-- Student Info -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                   
                                    <div>
                                        <div class="fw-medium">{{ $student->first_name }} {{ $student->last_name }}</div>
                                        <small class="text-muted">ID: {{ $student->user_id }}</small>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Contact Info -->
                            <td>
                                <div class="text-primary">{{ $student->email }}</div>
                                <small class="text-muted">{{ $student->phone_number ?? 'No phone' }}</small>
                            </td>
                             <td>
    
                                <small class="text-muted">{{ $student->address ?? 'No Address' }}</small>
                            </td>
                       
                            <!-- Actions -->
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end">
                                
                                <button type="button" class="btn btn-sm btn-soft-info me-2" data-bs-toggle="modal" data-bs-target="#studentModal{{ $student->user_id }}">
    <i class="fas fa-eye"></i>
</button>
                                    

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
            @foreach($students as $student)
<!-- Student Detail Modal -->
<div class="modal fade" id="studentModal{{ $student->user_id }}" tabindex="-1" aria-labelledby="studentModalLabel{{ $student->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel{{ $student->user_id }}">Student Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
          <dt class="col-sm-4">Full Name</dt>
          <dd class="col-sm-8">{{ $student->first_name }} {{ $student->last_name }}</dd>

          <dt class="col-sm-4">Email</dt>
          <dd class="col-sm-8">{{ $student->email }}</dd>

          <dt class="col-sm-4">Phone Number</dt>
          <dd class="col-sm-8">{{ $student->phone_number ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Student Number</dt>
          <dd class="col-sm-8">{{ $student->studentDetail->student_number ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Course</dt>
          <dd class="col-sm-8">{{ $student->studentDetail->course ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Faculty</dt>
          <dd class="col-sm-8">{{ $student->studentDetail->faculty ?? 'N/A' }}</dd>

          <dt class="col-sm-4">Address</dt>
          <dd class="col-sm-8">{{ $student->address ?? 'N/A' }}</dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach

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