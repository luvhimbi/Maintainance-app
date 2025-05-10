@extends('layouts.AdminNavBar')

@section('title', 'Technician Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Technician Details</h1>
        <div>
           
            <a href="{{ route('admin.technicians.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">firstname:</label>
                        <p>{{ $technician->first_name }}</p>
                    </div>
                      <div class="mb-3">
                        <label class="form-label fw-bold">lastname:</label>
                        <p>{{ $technician->last_name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <p>{{ $technician->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number:</label>
                        <p>{{ $technician->phone_number ?? 'N/A' }}</p>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Professional Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Specialization:</label>
                        <p>{{ $technician->maintenanceStaff->specialization }}</p>
                    </div>
                     <div class="mb-3">
                        <label class="form-label fw-bold">Availability_status:</label>
                        <p>{{ $technician->maintenanceStaff->availability_status }}</p>
                    </div>
                      <div class="mb-3">
                        <label class="form-label fw-bold">Current WorkLoad:</label>
                        <p class="me-2">{{ $technician->maintenanceStaff->current_workload }} tasks</p>
                    </div>
                    <div class="mb-3">
                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection