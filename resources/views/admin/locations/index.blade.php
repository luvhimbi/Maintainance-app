@extends('Layouts.AdminNavBar')
@section('title', 'View Locations')
@section('content')
<div class="container-fluid locations-management py-4">
    <div class="card border-0 shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center py-3 px-4">
            <div>
                <h2 class="h5 mb-1">Location Management</h2>
                <p class="text-muted small mb-0">Manage all building locations and rooms</p>
            </div>

        </div>

        <!-- Card Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Building</th>
                            <th>Floor</th>
                            <th>Room</th>
                            <th class="pe-4" width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                        <tr>
                            <!-- Building -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="icon-container text-primary me-2">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <span class="fw-medium">{{ $location->building_name }}</span>
                                </div>
                            </td>

                            <!-- Floor -->
                            <td>
                                <span class="badge bg-soft-secondary">
                                    Floor {{ $location->floor_number }}
                                </span>
                            </td>

                            <!-- Room -->
                            <td>
                                @if($location->room_number)
                                    <span class="badge bg-soft-info">
                                        Room {{ $location->room_number }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="pe-4">
                                <div class="d-flex">
                                    <!-- Edit Button -->


                                    <a href="{{ route('admin.locations.edit', $location->location_id) }}"
                                       class="btn btn-sm btn-soft-primary me-2"
                                       title="Edit">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.locations.destroy', $location->location_id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-soft-danger"
                                                title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this location?')">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="mb-1">No Locations Found</h5>
                                    <p class="text-muted small">Add your first location to get started</p>

                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination">
                 {{ $locations->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Base Styles */
    .locations-management {
        max-width: 1600px;
    }

    /* Card Header Styles */
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 500;
        color: #6c757d;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
        border-top: 1px solid #f8f9fa;
    }

    /* Badge Styles */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.75rem;
    }

    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-soft-secondary {
        background-color: rgba(1, 14, 26, 0.1);
        color: black;
    }

    .bg-soft-info {
        background-color: rgba(13, 98, 116, 0.623);
        color:black;
    }

    /* Button Styles */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .btn-soft-primary {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
        border: none;
    }

    .btn-soft-danger {
        color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
        border: none;
    }

    .btn-soft-primary:hover {
        background-color: rgba(13, 110, 253, 0.2);
    }

    .btn-soft-danger:hover {
        background-color: rgba(220, 53, 69, 0.2);
    }

    /* Icon container */
    .icon-container {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Empty State */
    .empty-state {
        padding: 2rem;
    }

    .empty-state i {
        opacity: 0.5;
    }
</style>
@endsection
