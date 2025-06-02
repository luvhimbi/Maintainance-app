@extends('layouts.StudentNavbar')

@section('title', 'Edit Issue')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                {{-- Main Card Header --}}
                <div class="card-header bg-white text-dark text-center py-4 rounded-top-4 border-bottom">
                    <h2 class="mb-0 fw-bold">Edit Issue #{{ $issue->issue_id }}</h2>
                    <p class="text-muted mb-0">Update the details for your reported issue.</p>
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                <div class="alert alert-danger mx-4 mt-4 rounded-3" role="alert">
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

                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                {{-- Location Section --}}
                                <div class="card mb-4 border shadow-sm rounded-3">
                                    <div class="card-header bg-light text-dark pt-4 pb-3 px-4 rounded-top-3 border-bottom">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2 text-secondary"></i>Location Information</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label for="location" class="form-label fw-bold">Select Location</label>
                                            <select class="form-select form-select-lg" id="location" name="location_id" onchange="autoFillDetails()" required>
                                                <option value="">Select a location</option>
                                                @foreach($locations as $location)
                                                    <option value="{{ $location->location_id }}"
                                                        {{ $issue->location_id == $location->location_id ? 'selected' : '' }}>
                                                        {{ $location->building_name }} - Floor {{ $location->floor_number }}, Room {{ $location->room_number }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="row g-3 mt-3">
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Building</label>
                                                <input type="text" class="form-control form-control-sm bg-light" id="building" name="building" value="{{ $issue->location->building_name ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Floor</label>
                                                <input type="text" class="form-control form-control-sm bg-light" id="floor" name="floor" value="{{ $issue->location->floor_number ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small text-muted">Room</label>
                                                <input type="text" class="form-control form-control-sm bg-light" id="room" name="room" value="{{ $issue->location->room_number ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Issue Classification --}}
                                <div class="card mb-4 border shadow-sm rounded-3">
                                    <div class="card-header bg-light text-dark pt-4 pb-3 px-4 rounded-top-3 border-bottom">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-tags me-2 text-secondary"></i>Issue Classification</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Issue Type</label>
                                            <div class="d-flex flex-wrap gap-2" id="issueTypeGroup">
                                                <button type="button" class="btn btn-outline-secondary issue-type-btn {{ $issue->issue_type == 'Electrical' ? 'active' : '' }}" data-value="Electrical">
                                                    <i class="fas fa-bolt me-1"></i> Electrical
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary issue-type-btn {{ $issue->issue_type == 'Structural' ? 'active' : '' }}" data-value="Structural">
                                                    <i class="fas fa-home me-1"></i> Structural
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary issue-type-btn {{ $issue->issue_type == 'HVAC' ? 'active' : '' }}" data-value="HVAC">
                                                    <i class="fas fa-wind me-1"></i> HVAC
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary issue-type-btn {{ $issue->issue_type == 'Plumbing' ? 'active' : '' }}" data-value="Plumbing">
                                                    <i class="fas fa-faucet me-1"></i> Plumbing
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary issue-type-btn {{ $issue->issue_type == 'Furniture' ? 'active' : '' }}" data-value="Furniture">
                                                    <i class="fas fa-couch me-1"></i> Furniture
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary issue-type-btn {{ $issue->issue_type == 'PC' ? 'active' : '' }}" data-value="PC">
                                                    <i class="fas fa-desktop me-1"></i> PC
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary issue-type-btn {{ $issue->issue_type == 'General' ? 'active' : '' }}" data-value="General">
                                                    <i class="fas fa-ellipsis-h me-1"></i> General
                                                </button>
                                            </div>
                                            <input type="hidden" id="issueType" name="issue_type" value="{{ $issue->issue_type }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="affected_areas" class="form-label fw-bold">Affected Areas / Quantity</label>
                                            <input type="number" class="form-control" id="affected_areas" name="affected_areas" min="1" value="{{ $issue->affected_areas }}" placeholder="e.g., 1, 2, or number of items" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Issue Characteristics --}}
                                <div class="card mb-4 border shadow-sm rounded-3">
                                    <div class="card-header bg-light text-dark pt-4 pb-3 px-4 rounded-top-3 border-bottom">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2 text-secondary"></i>Issue Characteristics</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="form-check form-switch mb-3">
                                            {{-- Hidden input to ensure '0' is sent if checkbox is unchecked --}}
                                            <input type="hidden" name="safety_hazard" value="0">
                                            <input class="form-check-input" type="checkbox" id="safetyHazard" name="safety_hazard" value="1" {{ $issue->safety_hazard ? 'checked' : '' }}>
                                            <label class="form-check-label" for="safetyHazard">This is a safety hazard</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                {{-- PC-Specific Fields (Conditionally displayed) --}}
                                <div class="card mb-4 border shadow-sm rounded-3" id="pcFields" style="display: {{ $issue->issue_type == 'PC' ? 'block' : 'none' }};">
                                    <div class="card-header bg-light text-dark pt-4 pb-3 px-4 rounded-top-3 border-bottom">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-desktop me-2 text-secondary"></i>PC-Specific Information</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label for="pc_number" class="form-label fw-bold">PC Number</label>
                                            <input type="text" class="form-control" id="pc_number" name="pc_number" value="{{ $issue->pc_number }}" placeholder="e.g., PC-001, Lab-PC-A">
                                        </div>

                                        <div class="mb-3">
                                            <label for="pc_issue_type" class="form-label fw-bold">PC Issue Type</label>
                                            <select class="form-select" id="pc_issue_type" name="pc_issue_type">
                                                <option value="">Select type</option>
                                                <option value="hardware" {{ $issue->pc_issue_type == 'hardware' ? 'selected' : '' }}>Hardware</option>
                                                <option value="software" {{ $issue->pc_issue_type == 'software' ? 'selected' : '' }}>Software</option>
                                                <option value="network" {{ $issue->pc_issue_type == 'network' ? 'selected' : '' }}>Network</option>
                                                <option value="peripheral" {{ $issue->pc_issue_type == 'peripheral' ? 'selected' : '' }}>Peripheral</option>
                                                <option value="other" {{ $issue->pc_issue_type == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="criticalWorkAffected" name="critical_work_affected" value="1" {{ $issue->critical_work_affected ? 'checked' : '' }}>
                                            <label class="form-check-label" for="criticalWorkAffected">Critical work is affected</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Issue Details --}}
                                <div class="card mb-4 border shadow-sm rounded-3">
                                    <div class="card-header bg-light text-dark pt-4 pb-3 px-4 rounded-top-3 border-bottom">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-secondary"></i>Issue Details</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label for="issue_description" class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" id="issue_description" name="issue_description" rows="7" placeholder="Please provide detailed information about the issue, including steps to reproduce it if applicable." required>{{ $issue->issue_description }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Attachments --}}
                                <div class="card border shadow-sm rounded-3">
                                    <div class="card-header bg-light text-dark pt-4 pb-3 px-4 rounded-top-3 border-bottom">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-paperclip me-2 text-secondary"></i>Attachments</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="file-upload-area text-center p-4 border-dashed rounded-3 bg-white mb-4">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="mb-3 text-muted">Drag & drop files here or click to browse</p>
                                            <input type="file" id="mediaUpload" name="attachments[]" hidden accept="image/*, video/*, .pdf, .doc, .docx" multiple>
                                            <label for="mediaUpload" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-plus-circle me-2"></i>Add Files
                                            </label>
                                            <p class="small text-muted mt-2 mb-0">Max file size: 10MB per file. Supported formats: JPG, PNG, PDF, DOC, DOCX, MP4</p>
                                            <div id="fileList" class="text-start mt-3"></div>
                                        </div>

                                        @if($issue->attachments->count() > 0)
                                            <div class="mt-4">
                                                <h6 class="fw-bold mb-3 text-secondary">Current Attachments:</h6>
                                                <div class="list-group">
                                                    @foreach($issue->attachments as $attachment)
                                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">
                                                            <div>
                                                                <i class="fas fa-file-alt me-2 text-info"></i>
                                                                <a href="{{ Storage::url($attachment->file_path) }}"
                                                                    target="_blank" class="text-decoration-none text-dark fw-medium">
                                                                    {{ $attachment->original_name }}
                                                                </a>
                                                            </div>
                                                            <span class="badge bg-light text-dark">{{ round($attachment->file_size / 1024, 1) }} KB</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="alert alert-info mt-3 p-2 small rounded-3" role="alert">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Note: Uploading new files will replace all existing attachments.
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="d-flex justify-content-end gap-3 mt-5">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                                <i class="fas fa-times-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-dark px-4 py-2 rounded-pill">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styles for a modern and simplistic look with white theme */
    body {
        background-color: #f8f9fa; /* Light grey background */
        min-height: 100vh; /* Ensure body takes full viewport height */
        display: flex; /* Enable flexbox */
        flex-direction: column; /* Arrange content in a column */
    }

    .card {
        border: 1px solid #e0e0e0; /* Subtle border for cards */
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05); /* Lighter shadow */
    }

    .card-header {
        background-color: #ffffff; /* White background for headers */
        color: #343a40; /* Dark text */
        border-bottom: 1px solid #e9ecef; /* Light border at the bottom */
    }

    .card-header h2, .card-header p {
        color: #343a40 !important; /* Ensure text is dark */
    }

    .card-header.bg-light {
        background-color: #f8f9fa !important; /* Very light grey for section headers */
        color: #343a40; /* Darker text for section headers */
        border-bottom: 1px solid #dee2e6;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .form-control, .form-select {
        border-radius: 0.5rem; /* Slightly more rounded inputs */
        border-color: #ced4da;
    }

    .form-control:focus, .form-select:focus {
        border-color: #b0b0b0; /* Grey focus border */
        box-shadow: 0 0 0 0.25rem rgba(128, 128, 128, 0.25); /* Grey shadow on focus */
    }

    .issue-type-btn {
        flex: 1 0 calc(33.333% - 0.5rem);
        min-width: 120px;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        color: #6c757d; /* Secondary color for outline buttons */
        border-color: #6c757d;
        background-color: #ffffff; /* White background */
    }

    .issue-type-btn:hover {
        background-color: #e9ecef; /* Light grey hover effect */
        color: #495057;
        border-color: #5a6268;
    }

    .issue-type-btn.active {
        background-color: #343a40; /* Dark grey for active state */
        color: white;
        border-color: #343a40;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
    }

    .file-upload-area {
        border: 2px dashed #d1d1d1; /* Lighter dashed border */
        background-color: #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: #a0a0a0; /* Darker grey on hover */
        background-color: #f0f0f0; /* Lighter grey background on hover */
    }

    .file-upload-area .fa-cloud-upload-alt {
        color: #adb5bd; /* Muted icon color */
    }

    .list-group-item {
        border-radius: 0.4rem;
        margin-bottom: 0.5rem;
        border: 1px solid #e9ecef;
        background-color: #fdfdfd;
    }

    .list-group-item:last-child {
        margin-bottom: 0;
    }

    .btn-dark {
        background-color: #343a40;
        border-color: #343a40;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .btn-dark:hover {
        background-color: #23272b;
        border-color: #1d2124;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        background-color: #ffffff;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
        border-color: #6c757d;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-fill location details on page load if a location is already selected
        if (document.getElementById('location').value) {
            autoFillDetails();
        }

        // Handle issue type selection
        const issueTypeGroup = document.getElementById('issueTypeGroup');
        const issueTypeInput = document.getElementById('issueType');
        const pcFields = document.getElementById('pcFields');

        // Initialize issueTypeInput value and PC fields display on page load
        const initialActiveButton = issueTypeGroup.querySelector('.issue-type-btn.active');
        if (initialActiveButton) {
            issueTypeInput.value = initialActiveButton.getAttribute('data-value');
            togglePcFields(); // Call to set initial visibility of PC fields
        }

        issueTypeGroup.querySelectorAll('.issue-type-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Remove 'active' from all buttons
                issueTypeGroup.querySelectorAll('.issue-type-btn').forEach(btn => btn.classList.remove('active'));
                // Add 'active' to the clicked button
                this.classList.add('active');
                // Set the hidden input's value
                const selectedValue = this.getAttribute('data-value');
                issueTypeInput.value = selectedValue;

                // Show/hide PC-specific fields based on selection
                togglePcFields(); // Call to update visibility after click
            });
        });

        // Function to show/hide PC-specific fields
        function togglePcFields() {
            const selectedIssueType = issueTypeInput.value;
            pcFields.style.display = selectedIssueType === 'PC' ? 'block' : 'none';

            // Set required attribute for PC fields based on visibility
            pcFields.querySelectorAll('[name="pc_number"], [name="pc_issue_type"], [name="critical_work_affected"]').forEach(field => {
                if (selectedIssueType === 'PC') {
                    // Only pc_number and pc_issue_type are required by validation in controller
                    // critical_work_affected is boolean and handled by hidden input
                    if (field.name === 'pc_number' || field.name === 'pc_issue_type') {
                        field.required = true;
                    }
                } else {
                    field.required = false;
                }
            });
        }


        // Handle file upload display and drag & drop
        const mediaUpload = document.getElementById('mediaUpload');
        const fileList = document.getElementById('fileList');
        const fileUploadArea = document.querySelector('.file-upload-area');

        function updateFileList(files) {
            fileList.innerHTML = ''; // Clear existing list
            if (files.length > 0) {
                fileUploadArea.classList.add('border-secondary'); // Highlight border when files are present
                Array.from(files).forEach(file => {
                    fileList.innerHTML += `
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded-3 shadow-sm-sm">
                            <span class="text-truncate me-2"><i class="fas fa-file me-2 text-muted"></i>${file.name}</span>
                            <span class="badge bg-light text-dark">${(file.size / 1024).toFixed(2)} KB</span>
                        </div>`;
                });
            } else {
                fileUploadArea.classList.remove('border-secondary'); // Remove highlight if no files
            }
        }

        mediaUpload.addEventListener('change', function () {
            updateFileList(this.files);
        });

        // Drag and drop events
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault(); // Prevent default to allow drop
            fileUploadArea.classList.add('border-secondary', 'bg-light'); // Visual feedback
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('border-secondary', 'bg-light'); // Remove visual feedback
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault(); // Prevent default file open behavior
            fileUploadArea.classList.remove('border-secondary', 'bg-light'); // Remove visual feedback

            // Assign dropped files to the file input
            mediaUpload.files = e.dataTransfer.files;

            // Manually trigger a change event on the file input to update the displayed list
            const event = new Event('change');
            mediaUpload.dispatchEvent(event);
        });

        // SweetAlert for success message with navigation options
        @if(session('swal_success_update'))
            Swal.fire({
                title: 'Success!',
                text: 'Issue updated successfully!',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Go to Issue Details',
                cancelButtonText: 'Stay on this page',
                reverseButtons: true, // Puts confirm button on the right
                customClass: {
                    confirmButton: 'btn btn-dark px-4 py-2 rounded-pill mx-2',
                    cancelButton: 'btn btn-outline-secondary px-4 py-2 rounded-pill mx-2'
                },
                buttonsStyling: false, // Disable default SweetAlert styling for buttons
                allowOutsideClick: false, // Prevent closing by clicking outside
                allowEscapeKey: false // Prevent closing by escape key
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('Student.issue_details', session('swal_success_update')) }}";
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // User clicked 'Stay on this page' or dismissed the modal
                    // No action needed, user stays on the current page
                }
            });
        @endif

        // SweetAlert for general error messages (if any)
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#343a40' // Dark button for error
            });
        @endif
    });

    // Function to auto-fill location details based on dropdown selection
    function autoFillDetails() {
        const locationSelect = document.getElementById('location');
        const selectedOption = locationSelect.options[locationSelect.selectedIndex];

        // Ensure an option is selected before attempting to split
        if (selectedOption && selectedOption.value) {
            const fullText = selectedOption.text;
            // Expected format: "Building Name - Floor X, Room Y"
            const parts = fullText.match(/(.*) - Floor (\d+), Room (.*)/);

            if (parts && parts.length === 4) {
                document.getElementById('building').value = parts[1].trim();
                document.getElementById('floor').value = parts[2].trim();
                document.getElementById('room').value = parts[3].trim();
            } else {
                // Fallback or clear if format doesn't match
                document.getElementById('building').value = '';
                document.getElementById('floor').value = '';
                document.getElementById('room').value = '';
            }
        } else {
            // Clear fields if "Select location" is chosen
            document.getElementById('building').value = '';
            document.getElementById('floor').value = '';
            document.getElementById('room').value = '';
        }
    }
</script>
@endpush
@endSection
