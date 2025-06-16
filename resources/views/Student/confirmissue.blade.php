@extends('layouts.StudentNavbar')

@section('title', 'Confirm Issue Report')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h3 class="text-center mb-4 fw-bold" >Confirm Your Maintenance Issue Report</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong style="color:black;">Please fix these issues:</strong>
                    <ul class="mb-0" style="color:black;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Issue Details Summary</h5>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">REPORT DATE</div>
                        <div class="info-value">{{ now()->format('M j, Y \a\t g:i A') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">LOCATION</div>
                        <div class="info-value">
                            <i class="fas fa-building me-1 text-secondary"></i> {{ $location->building_name }}
                            @if($location->floor_number)
                                <span class="mx-2">•</span>
                                <i class="fas fa-layer-group me-1 text-secondary"></i> Floor {{ $location->floor_number }}
                            @endif
                            @if($location->room_number)
                                <span class="mx-2">•</span>
                                <i class="fas fa-door-open me-1 text-secondary"></i> Room {{ $location->room_number }}
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">ISSUE TYPE</div>
                        <div class="info-value">
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $formData['issue_type'] }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">SAFETY HAZARD</div>
                        <div class="info-value">
                            @if($formData['safety_hazard'])
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Yes
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="fas fa-check-circle me-1"></i> No
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">AFFECTED AREAS</div>
                        <div class="info-value">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(explode(',', $formData['affected_areas']) as $area)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ trim($area) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @if($formData['issue_type'] === 'PC')
                        <div class="bg-light p-3 rounded mb-3">
                            <h6 class="fw-bold mb-3 text-primary">
                                <i class="fas fa-desktop me-2"></i>PC Specific Details
                            </h6>
                            <div class="mb-2">
                                <div class="info-label fw-bold text-muted small">PC NUMBER</div>
                                <div class="info-value">{{ $formData['pc_number'] }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label fw-bold text-muted small">PC ISSUE TYPE</div>
                                <div class="info-value">{{ $formData['pc_issue_type'] ?? 'Not specified' }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="info-label fw-bold text-muted small">CRITICAL WORK AFFECTED</div>
                                <div class="info-value">
                                    @if($formData['critical_work_affected'])
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Yes</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success">No</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">URGENCY LEVEL</div>
                        <div class="info-value">
                            @if($formData['urgency_level'] == 'High')
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation me-1"></i> High ({{ $formData['urgency_score'] ?? 'N/A' }})
                                </span>
                            @elseif($formData['urgency_level'] == 'Medium')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-arrow-up me-1"></i> Medium ({{ $formData['urgency_score'] ?? 'N/A' }})
                                </span>
                            @elseif($formData['urgency_level'] == 'Low')
                                <span class="badge bg-success">
                                    <i class="fas fa-arrow-down me-1"></i> Low ({{ $formData['urgency_score'] ?? 'N/A' }})
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">AFFECTS OPERATIONS</div>
                        <div class="info-value">
                            @if($formData['affects_operations'])
                                <span class="badge bg-danger bg-opacity-10 text-danger">Yes</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success">No</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label fw-bold text-muted small">DESCRIPTION</div>
                        <div class="border p-3 rounded bg-light bg-opacity-10" style="min-height: 100px;">
                            {{ $formData['issue_description'] }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <h5 class="mt-4 mb-3">
                            <i class="fas fa-paperclip me-2 text-primary"></i>Attachments
                            <small class="text-muted">({{ count($attachments) }})</small>
                        </h5>
                        @if (count($attachments) > 0)
                            <div class="row g-3">
                                @foreach ($attachments as $attachment)
                                    @php
                                        $ext = strtolower(pathinfo($attachment['original_name'], PATHINFO_EXTENSION));
                                        $icon = match($ext) {
                                            'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-info',
                                            'mp4', 'mov' => 'fa-file-video text-danger',
                                            'pdf' => 'fa-file-pdf text-danger',
                                            'doc', 'docx' => 'fa-file-word text-primary',
                                            'xls', 'xlsx' => 'fa-file-excel text-success',
                                            'zip', 'rar' => 'fa-file-archive text-warning',
                                            default => 'fa-file text-secondary'
                                        };
                                    @endphp
                                    <div class="col-12">
                                        <div class="file-item p-2 border rounded d-flex align-items-center">
                                            <div class="icon-wrapper me-3">
                                                <i class="fas {{ $icon }} fa-2x"></i>
                                            </div>
                                            <div class="file-info flex-grow-1">
                                                <div class="file-name text-truncate fw-medium">{{ $attachment['original_name'] }}</div>
                                                <div class="file-size text-muted small">{{ number_format($attachment['size'] / 1024, 2) }} KB</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border d-flex align-items-center">
                                <i class="fas fa-info-circle me-2 text-muted"></i>
                                <span class="text-muted">No attachments uploaded</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <a href="{{ route('Student.createissue') }}" class="btn btn-outline-secondary btn-lg flex-grow-1">
                            <i class="fas fa-edit me-2"></i> Edit Details
                        </a>
                        <form id="confirmForm" action="{{ route('issue.save') }}" method="POST" class="flex-grow-1">
                            @csrf
                            @foreach($formData as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <button type="submit" class="btn btn-primary btn-lg w-100 submit-btn" style="background-color:#4361ee; border-color:#4361ee;">
                                <i class="fas fa-paper-plane me-2"></i> Submit Issue
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --primary-color: #4361ee;
        --primary-hover: #3a56d4;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --text-dark: #212529;
        --text-medium: #495057;
        --text-light: #6c757d;
        --border-color: #e0e0e0;
        --bg-light: #f8f9fa;
    }
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-light);
        color: var(--text-dark);
    }
    .info-label {
        font-size: 0.8rem;
        margin-bottom: 0.2rem;
        color: #495057;
    }
    .info-value {
        font-size: 1rem;
        color: #212529;
    }
    .file-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: white;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }
    .file-item i {
        color: var(--primary-color);
        margin-right: 0.75rem;
        font-size: 1.2rem;
    }
    .progress-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
        width: 100%;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    .progress-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e0e0e0;
        z-index: 0;
    }
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
        z-index: 1;
    }
    .step:not(:last-child):after {
        display: none;
    }
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: white;
        border: 2px solid var(--border-color);
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    .step.active .step-number,
    .step.completed .step-number {
        color: white;
        background-color: #4361ee;
        border-color: #4361ee;
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.2);
    }
    .step-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-align: center;
        transition: color 0.3s ease;
    }
    .step.active .step-label,
    .step.completed .step-label {
        font-weight: 600;
        color: #4361ee;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.35em 0.65em;
        border-radius: 4px;
        font-weight: 600;
    }
    .bg-danger {
        background-color: var(--danger-color) !important;
    }
    .bg-warning {
        background-color: var(--warning-color) !important;
        color: var(--text-dark) !important;
    }
    .bg-success {
        background-color: var(--success-color) !important;
    }
    .submit-btn {
        position: relative;
        overflow: hidden;
    }
    .submit-btn:after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }
    .submit-btn:focus:not(:active)::after {
        animation: ripple 1s ease-out;
    }
    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }
    .card {
        border-radius: 0.75rem;
    }
    .card-header {
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }
    .alert {
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#4361ee'
        });
        @endif

        const confirmForm = document.getElementById('confirmForm');
        if (confirmForm) {
            confirmForm.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirm Submission',
                    text: 'Are you sure you want to submit this issue?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4361ee',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, submit it!',
                    cancelButtonText: 'No, go back'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading modal
                        Swal.fire({
                            title: 'Submitting...',
                            html: '<div class="spinner-border text-primary" role="status"></div><br>Please wait while we process your issue and send notifications.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                                // Actually submit the form now
                                confirmForm.submit();
                            }
                        });
                    }
                });
            });
        }
    });
</script>
@endpush
