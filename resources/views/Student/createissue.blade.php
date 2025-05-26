<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report an Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }

        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-danger {
            background-color: var (--danger-color);
            border-color: var(--danger-color);
        }

        .btn-group .btn {
            border: 1px solid #e0e0e0;
            background-color: white;
            color: #6c757d;
        }

        .btn-group .btn.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-group .btn-outline-success.active {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-group .btn-outline-warning.active {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-group .btn-outline-danger.active {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .split-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .split-section {
                grid-template-columns: 1fr;
            }
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
        }

        .upload-box {
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            height: 100%;
        }

        .upload-box:hover {
            border-color: var(--primary-color);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #343a40;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border: 1px solid #e0e0e0;
        }

        .file-item i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 20px;
            right: -50%;
            width: 100%;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 0;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .step.active .step-number {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
        }

        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1rem;
        }

        .submit-btn:hover {
            background-color: var(--primary-hover);
            color: white;
        }

        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
@extends('layouts.StudentNavbar')

@section('title', 'Report Issue')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="progress-steps mb-4 d-flex justify-content-center">
                <div class="step active">
                    <div class="step-number" style="background-color:#4361ee; border-color:#4361ee; color:white;">1</div>
                    <div class="step-label" style="color:#4361ee; font-weight:600;">Issue Details</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-label" style="color:black;">Confirm Details</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label" style="color:black;">Submission Complete</div>
                </div>
            </div>
            <h3 class="text-center mb-4 fw-bold" style="color:#4361ee;">Report a Maintenance Issue</h3>
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

            <form id="reportForm" action="{{ route('issue.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- Screen 1: Location & Classification -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body">
                                <h5 class="mb-3" ><i class="fas fa-map-marker-alt me-2"></i>Location Details</h5>
                                <div class="mb-3">
                                    <label class="form-label" style="color:black;">Select Location *</label>
                                    <select class="form-select border-primary" name="location_id" id="location_id" required>
                                        <option value="">-- Select location --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->location_id }}"
                                                {{ (old('location_id') ?? ($formData['location_id'] ?? '')) == $location->location_id ? 'selected' : '' }}>
                                                {{ $location->building_name }} - Floor {{ $location->floor_number }}, Room {{ $location->room_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row g-2 mb-4">
                                    <div class="col-4">
                                        <label class="form-label" style="color:black;">Building</label>
                                        <input type="text" class="form-control bg-light border-primary" id="building" value="{{ $formData['building'] ?? '' }}" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label" style="color:black;">Floor</label>
                                        <input type="text" class="form-control bg-light border-primary" id="floor" value="{{ $formData['floor'] ?? '' }}" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label" style="color:black;">Room</label>
                                        <input type="text" class="form-control bg-light border-primary" id="room" value="{{ $formData['room'] ?? '' }}" readonly>
                                    </div>
                                </div>
                                <h5 class="mb-3" ><i class="fas fa-exclamation-circle me-2"></i>Issue Classification</h5>
                                <label class="form-label" style="color:black;">Issue Type *</label>
                                <div class="d-flex flex-wrap gap-2 mb-3" id="issueTypeGroup">
                                    @php
                                        $types = ['Electrical', 'Plumbing', 'Structural', 'HVAC', 'Furniture', 'PC', 'General'];
                                        $icons = [
                                            'Electrical' => 'bolt',
                                            'Plumbing' => 'faucet',
                                            'Structural' => 'home',
                                            'HVAC' => 'fan',
                                            'Furniture' => 'couch',
                                            'PC' => 'desktop',
                                            'General' => 'tools'
                                        ];
                                        $selectedType = old('issue_type') ?? ($formData['issue_type'] ?? '');
                                    @endphp
                                    @foreach($types as $type)
                                        <button type="button"
                                            class="btn btn-outline-primary{{ $selectedType == $type ? ' active' : '' }}"
                                            data-value="{{ $type }}"
                                            style="border-color:#4361ee; color:black;">
                                            <i class="fas fa-{{ $icons[$type] }} me-1"></i> <span style="color:black;">{{ $type }}</span>
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" id="issueType" name="issue_type" value="{{ $selectedType }}" required>
                                <div id="pcFields" class="dynamic-section row g-3 mt-2" style="display: none;">
                                    <div class="col-12">
                                        <label class="form-label" style="color:black;">PC Number <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control border-primary" name="pc_number" min="1" max="100" value="{{ old('pc_number') }}">
                                        <small class="text-muted" style="color:#4361ee;">PC number must be between 1 and 100.</small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" style="color:black;">PC Issue Type</label>
                                        <select class="form-select border-primary" name="pc_issue_type">
                                            <option value="">-- Select type --</option>
                                            <option value="hardware" {{ old('pc_issue_type') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                                            <option value="software" {{ old('pc_issue_type') == 'software' ? 'selected' : '' }}>Software</option>
                                            <option value="network" {{ old('pc_issue_type') == 'network' ? 'selected' : '' }}>Network</option>
                                            <option value="peripheral" {{ old('pc_issue_type') == 'peripheral' ? 'selected' : '' }}>Peripheral</option>
                                            <option value="other" {{ old('pc_issue_type') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="critical_work_affected" id="critical_work"
                                                {{ old('critical_work_affected') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="critical_work" style="color:black;">
                                                Affects critical work
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-3">
                                    <div class="col-6">
                                        <label class="form-label" style="color:black;">Urgency Level *</label>
                                        <select class="form-select border-primary" name="urgency_level" id="urgencyLevel" required>
                                            <option value="">Select Urgency Level</option>
                                            <option value="High" {{ (old('urgency_level') ?? ($formData['urgency_level'] ?? '')) == 'High' ? 'selected' : '' }}>High</option>
                                            <option value="Medium" {{ (old('urgency_level') ?? ($formData['urgency_level'] ?? '')) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="Low" {{ (old('urgency_level') ?? ($formData['urgency_level'] ?? '')) == 'Low' ? 'selected' : '' }}>Low</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" style="color:black;">Safety Hazard</label>
                                        <select class="form-select border-primary" name="safety_hazard">
                                            <option value="0">No</option>
                                            <option value="1" {{ old('safety_hazard') == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label" style="color:black;">Affected Areas</label>
                                    <input type="number" class="form-control border-primary" name="affected_areas" value="{{ old('affected_areas', 1) }}" min="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Screen 2: Details & Attachments -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="mb-3" ><i class="fas fa-align-left me-2"></i>Issue Details</h5>
                                <div class="mb-3 flex-grow-1">
                                    <label class="form-label" style="color:black;">Description *</label>
                                    <textarea class="form-control border-primary" name="issue_description" rows="7" required>{{ old('issue_description') ?? ($formData['issue_description'] ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" style="color:black;">Attachments</label>
                                    <input type="file" class="form-control border-primary" id="mediaUpload" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx">
                                    <div class="form-text" style="color:#4361ee;">
                                        Upload images (jpg, jpeg, png, gif), videos (mp4), or documents (pdf, doc, docx). Max 5 files.
                                    </div>
                                    <div id="fileList" class="text-start mt-3">
                                        @if(isset($attachments) && is_array($attachments) && count($attachments))
                                            @foreach($attachments as $file)
                                                @php
                                                    $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                                    $icon = match($ext) {
                                                        'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                                                        'mp4' => 'fa-file-video',
                                                        'pdf' => 'fa-file-pdf',
                                                        'doc', 'docx' => 'fa-file-word',
                                                        default => 'fa-file'
                                                    };
                                                @endphp
                                                <div class="file-item">
                                                    <i class="fas {{ $icon }}"></i>
                                                    <span>{{ $file['original_name'] }}</span>
                                                    <span class="text-muted ms-2">({{ number_format($file['file_size'] / 1024, 2) }} KB)</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="d-grid mt-auto">
                                    <button type="submit" class="btn btn-primary btn-lg" style="background-color:#4361ee; border-color:#4361ee;">
                                        <i class="fas fa-paper-plane me-2"></i> Submit to Review
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- row -->
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress-steps .step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }
    .progress-steps .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        right: -50%;
        width: 100%;
        height: 2px;
        background-color: #e0e0e0;
        z-index: 0;
    }
    .progress-steps .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: white;
        border: 2px solid #e0e0e0;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    .progress-steps .step.active .step-number {
        background-color: #4361ee;
        border-color: #4361ee;
        color: white;
    }
    .progress-steps .step-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-align: center;
    }
    .progress-steps .step.active .step-label {
        color: #4361ee;
        font-weight: 600;
    }
    .file-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        background: white;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border: 1px solid #e0e0e0;
    }
    .file-item i {
        color: #4361ee;
        margin-right: 0.5rem;
    }
    .btn-outline-primary, .form-select, .form-control {
        border-color: #4361ee !important;
    }
    .btn-outline-primary {
        color: #4361ee !important;
    }
    .btn-outline-primary.active, .btn-outline-primary:active, .btn-outline-primary:focus, .btn-outline-primary:hover {
        background-color: #4361ee !important;
        color: #fff !important;
        border-color: #4361ee !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert for error messages
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#4361ee'
        });
    @endif

    // Issue type button group logic
    const issueTypeGroup = document.getElementById('issueTypeGroup');
    const issueTypeInput = document.getElementById('issueType');
    if (issueTypeInput.value) {
        const activeButton = issueTypeGroup.querySelector(`[data-value="${issueTypeInput.value}"]`);
        if (activeButton) {
            issueTypeGroup.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
            activeButton.classList.add('active');
        }
    }
    issueTypeGroup.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function () {
            issueTypeGroup.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            issueTypeInput.value = this.getAttribute('data-value');
            togglePcFields();
        });
    });

    // PC fields toggle logic
    function togglePcFields() {
        const pcFields = document.getElementById('pcFields');
        if (issueTypeInput.value === 'PC') {
            pcFields.style.display = 'flex';
            pcFields.querySelectorAll('[name="pc_number"], [name="pc_issue_type"]').forEach(field => field.required = true);
        } else {
            pcFields.style.display = 'none';
            pcFields.querySelectorAll('[name="pc_number"], [name="pc_issue_type"]').forEach(field => field.required = false);
        }
    }
    togglePcFields();

    // Location details autofill
    function updateLocationDetails() {
        const select = document.getElementById('location_id');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const text = selectedOption.text;
            const match = text.match(/^(.*?) - Floor (.*?), Room (.*?)$/);
            if (match) {
                document.getElementById('building').value = match[1];
                document.getElementById('floor').value = match[2];
                document.getElementById('room').value = match[3];
            }
        } else {
            document.getElementById('building').value = '';
            document.getElementById('floor').value = '';
            document.getElementById('room').value = '';
        }
    }
    document.getElementById('location_id').addEventListener('change', updateLocationDetails);
    updateLocationDetails();

    // File upload display with icon
    const mediaUpload = document.getElementById('mediaUpload');
    const fileList = document.getElementById('fileList');
    if (mediaUpload) {
        mediaUpload.addEventListener('change', function () {
            fileList.innerHTML = '';
            Array.from(this.files).forEach(file => {
                let ext = file.name.split('.').pop().toLowerCase();
                let icon = 'fa-file';
                if(['jpg','jpeg','png','gif'].includes(ext)) icon = 'fa-file-image';
                else if(ext === 'mp4') icon = 'fa-file-video';
                else if(ext === 'pdf') icon = 'fa-file-pdf';
                else if(['doc','docx'].includes(ext)) icon = 'fa-file-word';
                fileList.innerHTML += `<div class="file-item"><i class="fas ${icon}"></i> ${file.name} (${(file.size / 1024).toFixed(2)} KB)</div>`;
            });
        });
    }
});
</script>
@endpush
</body>
</html>
