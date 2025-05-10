@extends('Layouts.AdminNavBar')
@section('title', 'Manage Staff Members')

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <!-- Header -->
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h2 class="h5 mb-1">Staff Management</h2>
                    <p class="text-muted small mb-0">Manage all staff member accounts and information</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                    <!-- Additional filters/buttons here if needed -->
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Staff</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                        <tr class="border-top">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-medium">{{ $member->first_name }} {{ $member->last_name }}</div>
                                        <small class="text-muted">ID: {{ $member->user_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-primary">{{ $member->email }}</div>
                                <small class="text-muted">{{ $member->phone_number ?? 'No phone' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $member->staffDetail->department ?? 'N/A' }}</small>
                            </td>
                             <td>
                                <small class="text-muted">{{ $member->staffDetail->position_title ?? 'N/A' }}</small>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end">
                                    <!-- View Modal Button -->
                                    <button type="button" class="btn btn-sm btn-soft-info me-2" data-bs-toggle="modal" data-bs-target="#staffModal{{ $member->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Staff Modal -->
                        <div class="modal fade" id="staffModal{{ $member->id }}" tabindex="-1" aria-labelledby="staffModalLabel{{ $member->id }}" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="staffModalLabel{{ $member->id }}">Staff Member Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <dl class="row">
                                  <dt class="col-sm-4">Full Name</dt>
                                  <dd class="col-sm-8">{{ $member->first_name }} {{ $member->last_name }}</dd>

                                  <dt class="col-sm-4">Email</dt>
                                  <dd class="col-sm-8">{{ $member->email }}</dd>

                                  <dt class="col-sm-4">Phone Number</dt>
                                  <dd class="col-sm-8">{{ $member->phone_number ?? 'N/A' }}</dd>

                                  <dt class="col-sm-4">Department</dt>
                                  <dd class="col-sm-8">{{ $member->staffDetail->department ?? 'N/A' }}</dd>


                                  <dt class="col-sm-4">Position</dt>
                                  <dd class="col-sm-8">{{ $member->staffDetail->position_title ?? 'N/A' }}</dd>
                                  <dt class="col-sm-4">Address</dt>
                                  <dd class="col-sm-8">{{ $member->address ?? 'N/A' }}</dd>
                                </dl>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                    <h5 class="mb-1">No Staff Found</h5>
                                    <p class="text-muted small">Add your first staff member to get started</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staff->hasPages())
            <div class="card-footer bg-transparent border-0 pt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $staff->firstItem() }} to {{ $staff->lastItem() }} of {{ $staff->total() }} entries
                    </div>
                    <div>
                        {{ $staff->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection
