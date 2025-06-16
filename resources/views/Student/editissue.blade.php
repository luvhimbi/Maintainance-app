@extends('layouts.StudentNavbar')

@section('title', 'Edit Issue')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h3 class="text-center mb-4 fw-bold" style="color:black;">Edit Maintenance Issue</h3>

            <div id="error-messages" class="alert alert-danger d-none">
                <strong style="color:black;">Please fix these issues:</strong>
                <ul class="mb-0" style="color:black;"></ul>
            </div>
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
            @if(session('swal_success_update'))
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Issue Updated!',
                            text: 'Issue #{{ session('swal_success_update') }} was updated successfully.',
                            showCancelButton: true,
                            confirmButtonText: 'Stay Here',
                            cancelButtonText: 'View Issue details',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                // Redirect to view issue page
                                window.location.href = "{{ route('Student.issue_details', session('swal_success_update')) }}";
                            }
                        });
                    });
                </script>
            @endif


            <form id="editIssueForm" action="{{ route('Student.updateissue', $issue->issue_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-4 split-section">
                    <!-- Left Column: Location & Classification -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body">
                                <h5 class="mb-3 section-title" style="color:black;"><i class="fas fa-map-marker-alt me-2"></i>Location Details</h5>
                                <div class="mb-4">
                                    <h5 class="text-primary mb-3">Location Details</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="building_id" class="form-label">Building</label>
                                            <select class="form-select border-primary" name="building_id" id="building_id" required>
                                                <option value="">-- Select building --</option>
                                                @foreach($buildings as $building)
                                                    <option value="{{ $building->id }}" {{ $issue->building_id == $building->id ? 'selected' : '' }}>
                                                        {{ $building->building_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="floor_id" class="form-label">Floor *</label>
                                            <div class="position-relative">
                                                <select class="form-select border-primary" name="floor_id" id="floor_id" required {{ empty($issue->building_id) ? 'disabled' : '' }}>
                                                    <option value="">-- Select floor --</option>
                                                    @foreach($floors as $floor)
                                                        <option value="{{ $floor->id }}" {{ $issue->floor_id == $floor->id ? 'selected' : '' }}>
                                                            Floor {{ $floor->floor_number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div id="floorLoading" class="position-absolute top-50 end-0 translate-middle-y me-2 d-none">
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="room_id" class="form-label">Room *</label>
                                            <div class="position-relative">
                                                <select class="form-select border-primary" name="room_id" id="room_id" required {{ empty($issue->floor_id) ? 'disabled' : '' }}>
                                                    <option value="">-- Select room --</option>
                                                    @foreach($rooms as $room)
                                                        <option value="{{ $room->id }}" {{ $issue->room_id == $room->id ? 'selected' : '' }}>
                                                            Room {{ $room->room_number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div id="roomLoading" class="position-absolute top-50 end-0 translate-middle-y me-2 d-none">
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-3 section-title" style="color:black;"><i class="fas fa-exclamation-circle me-2"></i>Issue Classification</h5>
                                <label class="form-label" style="color:black;">Issue Type *</label>
                                <small class="text-danger d-block mb-2">
                                    ⚠️ Changing the issue type will reassign the issue to a new technician.
                                </small>

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
                                        $selectedType = $issue->issue_type ?? '';
                                    @endphp

                                    @foreach($types as $type)
                                        <button type="button"
                                                class="btn btn-outline-primary {{ $selectedType == $type ? 'active' : '' }}"
                                                data-value="{{ $type }}">
                                            <i class="fas fa-{{ $icons[$type] }} me-1"></i>
                                            <span style="color:black;">{{ $type }}</span>
                                        </button>
                                    @endforeach
                                </div>

                                <input type="hidden" id="issueType" name="issue_type" value="{{ $selectedType }}" required>

                                <!-- PC-specific fields - conditionally displayed -->
                                <div id="pcFields" class="dynamic-section row g-3 mt-2" style="display: none;">
                                    <div class="col-12">
                                        <label for="pc_number" class="form-label" style="color:black;">PC Number <span class="text-danger">*</span></label>
                                        <input type="number"
                                               class="form-control border-primary"
                                               name="pc_number"
                                               id="pc_number"
                                               min="1"
                                               max="100"
                                               value="{{ $issue->pc_number ?? '' }}">
                                        <small class="text-muted" style="color:#4361ee;">PC number must be between 1 and 100.</small>
                                    </div>
                                    <div class="col-12">
                                        <label for="pc_issue_type" class="form-label" style="color:black;">PC Issue Type</label>
                                        <select class="form-select border-primary" name="pc_issue_type" id="pc_issue_type">
                                            <option value="">-- Select type --</option>
                                            <option value="hardware" {{ ($issue->pc_issue_type ?? '') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                                            <option value="software" {{ ($issue->pc_issue_type ?? '') == 'software' ? 'selected' : '' }}>Software</option>
                                            <option value="network" {{ ($issue->pc_issue_type ?? '') == 'network' ? 'selected' : '' }}>Network</option>
                                            <option value="peripheral" {{ ($issue->pc_issue_type ?? '') == 'peripheral' ? 'selected' : '' }}>Peripheral</option>
                                            <option value="other" {{ ($issue->pc_issue_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="hidden" name="critical_work_affected" value="0">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="critical_work_affected"
                                                   id="critical_work"
                                                   value="1"
                                                {{ ($issue->critical_work_affected ?? 0) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="critical_work" style="color:black;">
                                                Affects critical work
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <div class="col-12">
                                        <label for="safety_hazard" class="form-label" style="color:black;">Safety Hazard</label>
                                        <select class="form-select border-primary" name="safety_hazard" id="safety_hazard">
                                            <option value="0" {{ ($issue->safety_hazard ?? 0) == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ ($issue->safety_hazard ?? 0) == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="form-check">
                                        <input type="hidden" name="affects_operations" value="0">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="affects_operations"
                                               id="affects_operations"
                                               value="1"
                                            {{ ($issue->affects_operations ?? 0) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="affects_operations" style="color:black;">
                                            Affects operations
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label for="affected_areas" class="form-label" style="color:black;">Affected Areas</label>
                                    <input type="number" class="form-control border-primary" name="affected_areas" id="affected_areas" value="{{ $issue->affected_areas ?? 1 }}" min="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Details & Attachments -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-body d-flex flex-column">
                                <h5 class="mb-3 section-title" style="color:black;"><i class="fas fa-align-left me-2"></i>Issue Details</h5>
                                <div class="mb-3 flex-grow-1">
                                    <label for="issue_description" class="form-label" style="color:black;">Description *</label>
                                    <textarea class="form-control border-primary" name="issue_description" id="issue_description" rows="7" required>{{ $issue->issue_description }}</textarea>
                                </div>
                                <!-- Assignee Details Section -->
                                <div class="card shadow-sm border-0 mt-4">
                                    <div class="card-header bg-light text-dark d-flex align-items-center">
                                        <i class="fas fa-user-cog me-2"></i>
                                        <h6 class="mb-0">Current Assigned Technician</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($issue->task && $issue->task->assignee)
                                            <div class="d-flex align-items-center">
                                                <img
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($issue->task->assignee->first_name . ' ' . $issue->task->assignee->last_name) }}&background=dddddd&color=000000&bold=true&size=128"
                                                    alt="Technician Avatar"
                                                    class="rounded-circle me-3"
                                                    style="width: 50px; height: 50px; object-fit: cover;">

                                                <div>
                                                    <h6 class="mb-1" style="color:#333;">
                                                        {{ $issue->task->assignee->first_name }} {{ $issue->task->assignee->last_name }}
                                                    </h6>
                                                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                                                        {{ $issue->task->assignee->email }}
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No technician assigned yet.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="mediaUpload" class="form-label" style="color:black;">Attachments</label>
                                    <input type="file" class="form-control border-primary" id="mediaUpload" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx">
                                    <div class="form-text" style="color:#4361ee;">
                                        Upload images (jpg, jpeg, png, gif) or  videos (mp4).
                                    </div>
                                    <div id="fileList" class="text-start mt-3">
                                        @if($issue->attachments && $issue->attachments->count())
                                            @foreach($issue->attachments as $file)
                                                @php
                                                    $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
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
                                                    <span>{{ $file->original_name }}</span>
                                                    <span class="text-muted ms-2">({{ number_format($file->file_size / 1024, 2) }} KB)</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="d-grid mt-auto">
                                    <button type="submit" class="btn btn-primary submit-btn">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End row -->
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --primary-color: #4361ee;
        --primary-light: #e0e6ff;
        --primary-hover: #3a56d4;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --text-dark: #2d3748;
        --text-medium: #4a5568;
        --text-light: #718096;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --radius-lg: 12px;
        --radius-md: 8px;
        --radius-sm: 4px;
    }
    .form-label {
        font-weight: 600;
        color: black;
        margin-bottom: 0.5rem;
        font-size: 0.925rem;
        display: block;
    }
    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        background-color: var(--bg-white);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-light);
    }
    .btn {
        border-radius: var(--radius-md);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn i {
        font-size: 1.1em;
    }
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
        border: none;
        color: white;
        box-shadow: var(--shadow-sm);
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    .btn-outline-primary {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        background: #fff;
    }
    .btn-outline-primary.active, .btn-outline-primary:active, .btn-outline-primary:focus, .btn-outline-primary:hover {
        background-color: var(--primary-color) !important;
        color: #fff !important;
        border-color: var(--primary-color) !important;
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
            border-radius: 0;
        }
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: black;
        display: flex;
        align-items: center;
        position: relative;
        padding-bottom: 0.75rem;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 3px;
    }
    .section-title i {
        margin-right: 0.75rem;
        color: var(--primary-color);
        font-size: 1.4em;
    }
    .file-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        background: var(--bg-white);
        border-radius: var(--radius-md);
        margin-bottom: 0.75rem;
        border: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }
    .file-item:hover {
        transform: translateX(2px);
        box-shadow: var(--shadow-sm);
    }
    .file-item i {
        color: var(--primary-color);
        margin-right: 0.75rem;
        font-size: 1.2em;
    }
    .file-name {
        flex: 1;
        font-weight: 500;
        color: var(--text-dark);
    }
    .submit-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
        color: white;
        font-weight: 600;
        padding: 1rem 2rem;
        border: none;
        border-radius: var(--radius-md);
        margin-top: 1.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 4px 6px rgba(67, 97, 238, 0.2);
        transition: all 0.2s ease;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(67, 97, 238, 0.25);
    }
    .alert {
        border-radius: var(--radius-md);
        padding: 1rem 1.5rem;
        border-left: 4px solid var(--primary-color);
        background-color: var(--bg-white);
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateForm = document.getElementById('editIssueForm');
    if (updateForm) {
        updateForm.addEventListener('submit', function() {
            Swal.fire({
                title: 'Updating Issue...',
                html: '<div class="spinner-border text-primary" role="status"></div><br>Please wait while we process your update and notify the technician(s).',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Building, Floor, and Room dropdowns
    const buildingSelect = document.getElementById('building_id');
    const floorSelect = document.getElementById('floor_id');
    const roomSelect = document.getElementById('room_id');
    const floorLoading = document.getElementById('floorLoading');
    const roomLoading = document.getElementById('roomLoading');

    // Handle building selection
    buildingSelect.addEventListener('change', function() {
        const buildingId = this.value;
        
        // Reset and disable floor and room dropdowns
        floorSelect.innerHTML = '<option value="">-- Select floor --</option>';
        roomSelect.innerHTML = '<option value="">-- Select room --</option>';
        floorSelect.disabled = true;
        roomSelect.disabled = true;

        if (buildingId) {
            // Show loading spinner
            floorLoading.classList.remove('d-none');
            
            // Fetch floors for the selected building
            fetch(`/buildings/${buildingId}/floors`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.success) {
                        response.data.forEach(floor => {
                            const option = document.createElement('option');
                            option.value = floor.id;
                            option.textContent = `Floor ${floor.floor_number}`;
                            floorSelect.appendChild(option);
                        });
                        floorSelect.disabled = false;
                        
                        // If we have a pre-selected floor, select it after loading
                        const preselectedFloor = '{{ $issue->floor_id }}';
                        if (preselectedFloor) {
                            floorSelect.value = preselectedFloor;
                            floorSelect.dispatchEvent(new Event('change'));
                        }
                    } else {
                        console.error('Error:', response.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching floors:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load floors. Please try again.',
                        confirmButtonColor: '#4361ee'
                    });
                })
                .finally(() => {
                    floorLoading.classList.add('d-none');
                });
        }
    });

    // Handle floor selection
    floorSelect.addEventListener('change', function() {
        const floorId = this.value;
        
        // Reset and disable room dropdown
        roomSelect.innerHTML = '<option value="">-- Select room --</option>';
        roomSelect.disabled = true;

        if (floorId) {
            // Show loading spinner
            roomLoading.classList.remove('d-none');
            
            // Fetch rooms for the selected floor
            fetch(`/floors/${floorId}/rooms`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.success) {
                        response.data.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = `Room ${room.room_number}`;
                            roomSelect.appendChild(option);
                        });
                        roomSelect.disabled = false;
                        
                        // If we have a pre-selected room, select it after loading
                        const preselectedRoom = '{{ $issue->room_id }}';
                        if (preselectedRoom) {
                            roomSelect.value = preselectedRoom;
                        }
                    } else {
                        console.error('Error:', response.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching rooms:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load rooms. Please try again.',
                        confirmButtonColor: '#4361ee'
                    });
                })
                .finally(() => {
                    roomLoading.classList.add('d-none');
                });
        }
    });

    // If a building is pre-selected, trigger the change event
    if (buildingSelect.value) {
        buildingSelect.dispatchEvent(new Event('change'));
    }

    // Issue type button group logic
    const issueTypeGroup = document.getElementById('issueTypeGroup');
    const issueTypeInput = document.getElementById('issueType');
    const pcFields = document.getElementById('pcFields');
    const pcNumberInput = document.getElementById('pc_number');
    const pcIssueTypeSelect = document.getElementById('pc_issue_type');

    function togglePcFields() {
        if (issueTypeInput.value === 'PC') {
            pcFields.style.display = 'flex';
            pcNumberInput.setAttribute('required', 'true');
            pcIssueTypeSelect.setAttribute('required', 'true');
        } else {
            pcFields.style.display = 'none';
            pcNumberInput.removeAttribute('required');
            pcIssueTypeSelect.removeAttribute('required');
            pcNumberInput.value = '';
            pcIssueTypeSelect.value = '';
            document.getElementById('critical_work').checked = false;
        }
    }

    // Set initial state
    const initialIssueType = issueTypeInput.value;
    if (initialIssueType) {
        const activeButton = issueTypeGroup.querySelector(`[data-value="${initialIssueType}"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
    }
    togglePcFields();

    // Add event listeners to issue type buttons
    issueTypeGroup.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function () {
            issueTypeGroup.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            issueTypeInput.value = this.getAttribute('data-value');
            togglePcFields();
        });
    });

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
                fileList.innerHTML += `
                    <div class="file-item">
                        <i class="fas ${icon}"></i>
                        <span>${file.name}</span>
                        <span class="text-muted ms-2">(${(file.size / 1024).toFixed(2)} KB)</span>
                    </div>
                `;
            });
        });
    }

    // Client-side validation
    const editIssueForm = document.getElementById('editIssueForm');
    editIssueForm.addEventListener('submit', function(event) {
        let errors = [];
        if (!buildingSelect.value) {
            errors.push('Please select a building.');
        }
        if (!issueTypeInput.value) {
            errors.push('Please select an issue type.');
        }
        if (!document.getElementById('issue_description').value.trim()) {
            errors.push('Please provide a description for the issue.');
        }
        if (issueTypeInput.value === 'PC') {
            if (!pcNumberInput.value) {
                errors.push('PC Number is required for PC issues.');
            }
            if (!pcIssueTypeSelect.value) {
                errors.push('PC Issue Type is required for PC issues.');
            }
        }

        const errorMessagesDiv = document.getElementById('error-messages');
        const errorList = errorMessagesDiv.querySelector('ul');
        errorList.innerHTML = '';

        if (errors.length > 0) {
            event.preventDefault();
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            errorMessagesDiv.classList.remove('d-none');
            Swal.fire({
                icon: 'error',
                title: 'Validation Error!',
                html: 'Please correct the following:<br>' + errors.map(e => `&bull; ${e}`).join('<br>'),
                confirmButtonColor: '#4361ee'
            });
        } else {
            errorMessagesDiv.classList.add('d-none');
        }
    });
});
</script>
@endpush
