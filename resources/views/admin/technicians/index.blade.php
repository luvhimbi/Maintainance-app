@extends('layouts.AdminNavBar')

@section('title', 'Manage Technicians')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Maintenance Staff</h1>
        <a href="{{ route('admin.technicians.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Technician
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $tech)
                        <tr>
                            <td>{{ $tech->user_id }}</td>
                            <td>{{ $tech->username }}</td>
                            <td>{{ $tech->email }}</td>
                            <td>
                                @if($tech->technician)
                                    {{ $tech->technician->specialization }}
                                @else
                                    <span class="text-muted">No specialization</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $tech->status == 'Active' ? 'success' : ($tech->status == 'Suspended' ? 'warning' : 'secondary') }}">
                                    {{ $tech->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.technicians.show', $tech->user_id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.technicians.edit', $tech->user_id) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.technicians.destroy', $tech->user_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this technician?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $technicians->links() }}
            </div>
        </div>
    </div>
</div>
@endsection