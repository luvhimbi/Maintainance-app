@extends('layouts.StudentNavbar')

@section('title', 'Edit Issue')

@section('content')
    <div class="form-container">
        <h3 class="text-center mb-4">Edit Issue #{{ $issue->issue_id }}</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('Student.updateissue', $issue->issue_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                            @foreach($locations as $location)
                                <option value="{{ $location->location_id }}"
                                    {{ $issue->location_id == $location->location_id ? 'selected' : '' }}>
                                    {{ $location->building_name }} - Floor {{ $location->floor_number }}, Room {{ $location->room_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Building</label>
                            <input type="text" class="form-control bg-light" id="building" name="building" value="{{ $issue->location->building_name ?? '' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Floor</label>
                            <input type="text" class="form-control bg-light" id="floor" name="floor" value="{{ $issue->location->floor_number ?? '' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room</label>
                            <input type="text" class="form-control bg-light" id="room" name="room" value="{{ $issue->location->room_number ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="fas fa-exclamation-triangle"></i> Issue Classification
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Issue Type</label>
                        <div class="btn-group w-100" id="issueTypeGroup" role="group">
                            <button type="button" class="btn btn-outline-primary {{ $issue->issue_type == 'Electrical' ? 'active' : '' }}" data-value="Electrical">
                                <i class="fas fa-bolt me-1"></i> Electrical
                            </button>
                            <button type="button" class="btn btn-outline-primary {{ $issue->issue_type == 'Plumbing' ? 'active' : '' }}" data-value="Plumbing">
                                <i class="fas fa-faucet me-1"></i> Plumbing
                            </button>
                            <button type="button" class="btn btn-outline-primary {{ $issue->issue_type == 'Structural' ? 'active' : '' }}" data-value="Structural">
                                <i class="fas fa-home me-1"></i> Structural
                            </button>
                            <button type="button" class="btn btn-outline-primary {{ $issue->issue_type == 'General' ? 'active' : '' }}" data-value="General">
                                <i class="fas fa-ellipsis-h me-1"></i> General
                            </button>
                        </div>
                        <input type="hidden" id="issueType" name="issue_type" value="{{ $issue->issue_type }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Urgency Level</label>
                        <div class="btn-group w-100" id="urgencyGroup" role="group">
                            <button type="button" class="btn btn-outline-success {{ $issue->urgency_level == 'Low' ? 'active' : '' }}" data-value="Low">
                                <i class="fas fa-check-circle me-1"></i> Low
                            </button>
                            <button type="button" class="btn btn-outline-warning {{ $issue->urgency_level == 'Medium' ? 'active' : '' }}" data-value="Medium">
                                <i class="fas fa-exclamation-circle me-1"></i> Medium
                            </button>
                            <button type="button" class="btn btn-outline-danger {{ $issue->urgency_level == 'High' ? 'active' : '' }}" data-value="High">
                                <i class="fas fa-fire me-1"></i> High
                            </button>
                        </div>
                        <input type="hidden" id="urgencyLevel" name="urgency_level" value="{{ $issue->urgency_level }}" required>
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
                            <textarea class="form-control" id="description" name="issue_description" rows="5" placeholder="Please provide detailed information about the issue you're experiencing..." required>{{ $issue->issue_description }}</textarea>
                        </div>

                        <div class="file-upload-area text-center">
                            <div class="mb-3">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                <p class="mt-2">Update attachments for better reference</p>
                            </div>

                            <input type="file" id="mediaUpload" name="attachments[]" hidden accept="image/*, video/*, .pdf, .doc, .docx" multiple>
                            <label for="mediaUpload" class="btn btn-outline-primary mb-3">
                                <i class="fas fa-paperclip me-2"></i>Choose Files
                            </label>
                            <p class="small text-muted">Supported formats: Images, Videos, PDF, DOC</p>
                            <div id="fileList" class="text-start mt-3"></div>

                            @if($issue->attachments->count() > 0)
                                <div class="mt-4">
                                    <h6 class="fw-bold">Current Attachments:</h6>
                                    <div class="list-group">
                                        @foreach($issue->attachments as $attachment)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-paperclip me-2"></i>
                                                    <a href="{{ Storage::url($attachment->file_path) }}"
                                                       target="_blank" class="text-decoration-none">
                                                        {{ $attachment->original_name }}
                                                    </a>
                                                </div>
                                                <span class="badge bg-light text-dark">{{ round($attachment->file_size / 1024, 1) }} KB</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="alert alert-warning mt-2 p-2 small">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Uploading new files will replace all existing attachments.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-outline-success submit-btn">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('Student.issue_details', $issue->issue_id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .form-container {
            max-width: 1000px;
            margin: 2rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
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
            color: #4361ee;
        }

        .split-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
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
            border-color: #4361ee;
        }

        .btn-group .btn {
            border: 1px solid #e0e0e0;
            background-color: white;
            color: #6c757d;
        }

        .btn-group .btn.active {
            background-color: #4361ee;
            border-color: #4361ee;
            color: white;
        }

        .btn-group .btn-outline-success.active {
            background-color: #2ecc71;
            border-color: #2ecc71;
            color: white;
        }

        .btn-group .btn-outline-warning.active {
            background-color: #f39c12;
            border-color: #f39c12;
            color: white;
        }

        .btn-group .btn-outline-danger.active {
            background-color: #e74c3c;
            border-color: #e74c3c;
            color: white;
        }

        .submit-btn {
            background-color: #4361ee;
            color: white;
            font-weight: 600;
            padding: 1rem;
        }

        .submit-btn:hover {
            background-color: #3a56d4;
            color: white;
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-fill location details
            if (document.getElementById('location').value) {
                autoFillDetails();
            }

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
@endsection
