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
            background-color: var(--danger-color);
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
    <div class="form-container">
        <div class="progress-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-label">Report Issue</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">Confirm Details</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Submission Complete</div>
            </div>
        </div>

        <h3 class="text-center mb-4">Report an Issue</h3>

        <!-- Display validation errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="reportForm" action="{{ route('issue.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="split-section">
                <!-- Left Column -->
                <div class="left-column">
                    <div class="section-title">
                        <i class="fas fa-map-marker-alt"></i> Location Information
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Location</label>
                        <select class="form-select" id="location" name="location_id" onchange="autoFillDetails()" required>
                            <option value="">Select location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->location_id }}" {{ (isset($formData['location_id']) && $formData['location_id'] == $location->location_id) ? 'selected' : '' }}>
                                    {{ $location->building_name }} - Floor {{ $location->floor_number }}, Room {{ $location->room_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Building</label>
                            <input type="text" class="form-control bg-light" id="building" name="building" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Floor</label>
                            <input type="text" class="form-control bg-light" id="floor" name="floor" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room</label>
                            <input type="text" class="form-control bg-light" id="room" name="room" readonly>
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="fas fa-exclamation-triangle"></i> Issue Classification
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Issue Type</label>
                        <div class="btn-group w-100" id="issueTypeGroup" role="group">
                            <button type="button" class="btn btn-outline-primary" data-value="Electrical">
                                <i class="fas fa-bolt me-1"></i> Electrical
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-value="Plumbing">
                                <i class="fas fa-faucet me-1"></i> Plumbing
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-value="Structural">
                                <i class="fas fa-home me-1"></i> Structural
                            </button>
                        </div>
                        <input type="hidden" id="issueType" name="issue_type" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Urgency Level</label>
                        <div class="btn-group w-100" id="urgencyGroup" role="group">
                            <button type="button" class="btn btn-outline-success" data-value="Low">
                                <i class="fas fa-check-circle me-1"></i> Low
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-value="Medium">
                                <i class="fas fa-exclamation-circle me-1"></i> Medium
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-value="High">
                                <i class="fas fa-fire me-1"></i> High
                            </button>
                        </div>
                        <input type="hidden" id="urgencyLevel" name="urgency_level" required>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-column">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i> Issue Details
                    </div>

                    <div class="upload-box">
                        <div class="mb-4">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="issue_description" rows="5" placeholder="Please provide detailed information about the issue you're experiencing..." required></textarea>
                        </div>

                        <div class="file-upload-area text-center">
                            <div class="mb-3">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                <p class="mt-2">Upload photos or documents for better reference</p>
                            </div>

                            <input type="file" id="mediaUpload" name="attachments[]" hidden accept="image/*, video/*, .pdf, .doc, .docx" multiple>
                            <label for="mediaUpload" class="btn btn-outline-primary mb-3">
                                <i class="fas fa-paperclip me-2"></i>Choose Files
                            </label>
                            <p class="small text-muted">Supported formats: Images, Videos, PDF, DOC</p>
                            <div id="fileList" class="text-start mt-3"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-outline-success submit-btn">
                            <i class="fas fa-arrow-right me-2"></i>Review and Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endsection
    <script>
        // JavaScript for dynamic behavior
        document.addEventListener('DOMContentLoaded', function () {
            // Handle issue type selection
            const issueTypeGroup = document.getElementById('issueTypeGroup');
            const issueTypeInput = document.getElementById('issueType');
            issueTypeGroup.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function () {
                    issueTypeGroup.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    issueTypeInput.value = this.getAttribute('data-value');
                });
            });

            // Handle urgency level selection
            const urgencyGroup = document.getElementById('urgencyGroup');
            const urgencyLevelInput = document.getElementById('urgencyLevel');
            urgencyGroup.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function () {
                    urgencyGroup.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    urgencyLevelInput.value = this.getAttribute('data-value');
                });
            });

            // Handle file upload display
            const mediaUpload = document.getElementById('mediaUpload');
            const fileList = document.getElementById('fileList');
            mediaUpload.addEventListener('change', function () {
                fileList.innerHTML = '';
                Array.from(this.files).forEach(file => {
                    fileList.innerHTML += `<div>${file.name} (${(file.size / 1024).toFixed(2)} KB)</div>`;
                });
            });
        });

        // Auto-fill location details
        function autoFillDetails() {
            const locationSelect = document.getElementById('location');
            const selectedOption = locationSelect.options[locationSelect.selectedIndex].text;
            const [building, floor, room] = selectedOption.split(/ - Floor |, Room /);
            document.getElementById('building').value = building;
            document.getElementById('floor').value = floor;
            document.getElementById('room').value = room;
        }
    </script>
</body>
</html>
