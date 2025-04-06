@extends('layouts.AdminNavBar')

@section('title', 'Manage Technicians')

@section('content')
<div class="container-fluid technician-management py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h2 class="h5 mb-1">Manage Maintenance Staff</h2>
                    <p class="text-muted small mb-0">Manage all technician accounts and information</p>
                </div>
                
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('admin.technicians.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New Technician
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Card Body -->
        <div class="card-body p-0">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $tech)
                        <tr class="border-top">
                            <td class="ps-4">{{ $tech->user_id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-light-primary text-primary me-3">
                                        {{ substr($tech->username, 0, 1) }}
                                    </div>
                                    <div class="fw-medium">{{ $tech->username }}</div>
                                </div>
                            </td>
                            <td>{{ $tech->email }}</td>
                            <td>
                                @if($tech->maintenanceStaff)
                                    {{ $tech->maintenanceStaff->specialization }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge py-2 px-3 rounded-pill 
                                    @if($tech->status == 'Active') bg-soft-success text-success
                                    @elseif($tech->status == 'Suspended') bg-soft-warning text-warning
                                    @else bg-soft-secondary text-secondary @endif">
                                    <i class="fas fa-circle me-1" style="font-size: 6px; vertical-align: middle;"></i>
                                    {{ $tech->status }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end">
                                    
                                    <a href="{{ route('admin.technicians.edit', $tech->user_id) }}" 
                                       class="btn btn-sm btn-soft-primary me-2"
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.technicians.destroy', $tech->user_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-soft-danger delete-technician"
                                                data-name="{{ $tech->username }}"
                                                data-bs-toggle="tooltip" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($technicians->hasPages())
            <div class="card-footer bg-transparent border-0 pt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $technicians->firstItem() }} to {{ $technicians->lastItem() }} of {{ $technicians->total() }} entries
                    </div>
                    <div>
                        {{ $technicians->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Your existing styles here */
</style>
@endpush

@push('scripts')
<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Delete technician with SweetAlert confirmation
        document.querySelectorAll('.delete-technician').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const technicianName = this.getAttribute('data-name');
                
                Swal.fire({
                    title: 'Delete Technician?',
                    html: `Are you sure you want to delete <strong>${technicianName}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection