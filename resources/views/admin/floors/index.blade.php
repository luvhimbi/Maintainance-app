@extends('Layouts.AdminNavBar')

@section('title', 'Manage Floors')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Floors List</h5>
                    <a href="{{ route('admin.floors.create') }}" class="btn btn-primary">
                        Add New Floor
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
                                    <th class="py-3">Floor Name</th>
                                    <th class="py-3">Building</th>
                                    <th class="py-3">Total Rooms</th>
                                    <th class="py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($floors as $floor)
                                    <tr>
                                        <td>{{ $floor->floor_number }}</td>
                                        <td>{{ $floor->building->building_name }}</td>
                                        <td>{{ $floor->rooms->count() }} Rooms</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.floors.edit', $floor->id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Edit Floor">
                                                    Edit
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete({{ $floor->id }})"
                                                        title="Delete Floor">
                                                    Delete
                                                </button>
                                                <form id="delete-form-{{ $floor->id }}"
                                                      action="{{ route('admin.floors.destroy', $floor->id) }}"
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
                                            No floors found
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
function confirmDelete(floorId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the floor and all its associated rooms!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + floorId).submit();
        }
    });
}
</script>
@endpush
@endsection
