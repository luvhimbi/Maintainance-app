@extends('Layouts.AdminNavBar')

@section('title', 'Manage Buildings')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Buildings List</h5>
                    <a href="{{ route('admin.buildings.create') }}" class="btn btn-primary">
                        Add New Building
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">Building Name</th>
                                    <th class="py-3">Total Floors</th>
                                    <th class="py-3">Coordinates</th>
                                    <th class="py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($buildings as $building)
                                    <tr>
                                        <td>{{ $building->building_name }}</td>
                                        <td>{{ $building->floors->count() }} Floors</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <small>Lat: {{ number_format($building->latitude, 6) }}</small>
                                                <small>Long: {{ number_format($building->longitude, 6) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.buildings.edit', $building->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Building">
                                                    Edit
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete({{ $building->id }})"
                                                        title="Delete Building">
                                                    Delete
                                                </button>
                                                <form id="delete-form-{{ $building->id }}" 
                                                      action="{{ route('admin.buildings.destroy', $building->id) }}" 
                                                      method="POST" 
                                                      style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            No buildings found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(buildingId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the building and all its associated floors and rooms!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + buildingId).submit();
        }
    });
}
</script>
@endpush
@endsection 