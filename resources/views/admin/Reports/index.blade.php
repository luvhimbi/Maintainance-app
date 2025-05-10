@extends('layouts.AdminNavBar')

@section('title', 'Reports')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Generate Reports</h5>
                </div>
                <div class="card-body">
                    <!-- Task Report -->
                    <div class="report-section mb-4">
                        <h6 class="fw-bold mb-3">Task Report</h6>
                        <form action="{{ route('admin.reports.tasks') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Format</label>
                                <select class="form-select" name="type">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Generate Task Report
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Technician Report -->
                    <div class="report-section mb-4">
                        <h6 class="fw-bold mb-3">Technician Performance Report</h6>
                        <form action="{{ route('admin.reports.technicians') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Format</label>
                                <select class="form-select" name="type">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Generate Technician Report
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Performance Report -->
                    <div class="report-section">
                        <h6 class="fw-bold mb-3">Overall Performance Report</h6>
                        <form action="{{ route('admin.reports.performance') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Format</label>
                                <select class="form-select" name="type">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Generate Performance Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    .report-section {
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .report-section:hover {
        background: #fff;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 1rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.1);
    }

    .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-primary {
        background: #3a7bd5;
        border: none;
    }

    .btn-primary:hover {
        background: #2c6cb0;
    }
</style>
@endsection
@endsection 