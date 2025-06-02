@extends('layouts.AdminNavBar')

@section('title', 'Technician Details')

@section('content')
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h5 mb-1">Technician Details: {{ $technician->first_name }} {{ $technician->last_name }}</h2>
                        <p class="text-muted small mb-0">View comprehensive information about this technician.</p>
                    </div>
                    <div class="d-flex">
                        <a href="{{ route('admin.technicians.edit', $technician->user_id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit Technician
                        </a>
                        <a href="{{ route('admin.technicians.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body px-4">
                <div class="row">
                    {{-- User Details --}}
                    <div class="col-md-6 mb-4">
                        <h4 class="h6 text-primary mb-3">Personal Information</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>First Name:</strong>
                                <span>{{ $technician->first_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Last Name:</strong>
                                <span>{{ $technician->last_name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Email:</strong>
                                <span>{{ $technician->email }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Phone Number:</strong>
                                <span>{{ $technician->phone_number ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Address:</strong>
                                <span>{{ $technician->address ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Technician Specific Details --}}
                    <div class="col-md-6 mb-4">
                        <h4 class="h6 text-primary mb-3">Technician Specifics</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Specialization:</strong>
                                <span>{{ $technician->maintenanceStaff->specialization ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Availability Status:</strong>
                                <span>{{ $technician->maintenanceStaff->availability_status ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Current Workload:</strong>
                                <span>{{ $technician->maintenanceStaff->current_workload ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Account Created:</strong>
                                <span>{{ $technician->created_at ? $technician->created_at->format('M d, Y H:i A') : 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Last Updated:</strong>
                                <span>{{ $technician->updated_at ? $technician->updated_at->format('M d, Y H:i A') : 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end">
                    {{-- You can add a delete button here with a confirmation dialog if needed --}}
                    {{-- <form action="{{ route('admin.technicians.destroy', $technician->user_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this technician?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger me-2">
                            <i class="fas fa-trash-alt me-1"></i> Delete Technician
                        </button>
                    </form> --}}
                    <a href="{{ route('admin.technicians.index') }}" class="btn btn-outline-secondary">Back to Technicians List</a>
                </div>
            </div>
        </div>
    </div>
@endsection
