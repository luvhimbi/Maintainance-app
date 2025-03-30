@extends('Layouts.AdminNavBar')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h3 class="h5 mb-0">Manage Locations</h3>
            <a href="{{ route('admin.locations.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add Location
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Building</th>
                            <th>Floor</th>
                            <th>Room</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                        <tr>
                            <td>{{ $location->building_name }}</td>
                            <td>{{ $location->floor_number }}</td>
                            <td>{{ $location->room_number }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.locations.edit', $location->location_id) }}" 
                                       class="btn btn-outline-secondary"
                                       title="Edit">
                                        <i class="fas fa-edit">Edit</i>
                                    </a>
                                    
                                    <form action="{{ route('admin.locations.destroy', $location->location_id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger"
                                                title="Delete"
                                                onclick="return confirm('Delete this location?')">
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                                <p>No locations found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection