@extends('layouts.StudentNavbar')

@section('title', 'Edit Issue')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-white border-bottom-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="fas fa-edit text-primary "></i>
                            </div>
                            <div>
                                <h2 class="h4 mb-0 fw-bold text-dark">Edit Issue #{{ $issue->issue_id }}</h2>
                                <p class="text-muted mb-0">Update the details of your reported issue</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body px-4 py-4">
                        <form action="{{ route('Student.updateissue', $issue->issue_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Location Selection -->
                            <div class="mb-4">
                                <label for="location_id" class="form-label fw-semibold text-dark">Location <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                    <option value="">Select a location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}" 
                                            {{ $issue->location_id == $location->location_id ? 'selected' : '' }}>
                                            {{ $location->building_name }}, Room {{ $location->room_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Issue Type -->
                            <div class="mb-4">
                                <label for="issue_type" class="form-label fw-semibold text-dark">Issue Type <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg @error('issue_type') is-invalid @enderror" id="issue_type" name="issue_type" required>
                                    <option value="">Select issue type</option>
                                    <option value="Plumbing" {{ $issue->issue_type == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
                                    <option value="Electrical" {{ $issue->issue_type == 'Electrical' ? 'selected' : '' }}>Electrical</option>
                                    <option value="Structural" {{ $issue->issue_type == 'Structural' ? 'selected' : '' }}>Structural</option>
                                </select>
                                @error('issue_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Urgency Level -->
                            <div class="mb-4">
                                <label for="urgency_level" class="form-label fw-semibold text-dark">Urgency Level <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg @error('urgency_level') is-invalid @enderror" id="urgency_level" name="urgency_level" required>
                                    <option value="">Select urgency level</option>
                                    <option value="High" {{ $issue->urgency_level == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Medium" {{ $issue->urgency_level == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Low" {{ $issue->urgency_level == 'Low' ? 'selected' : '' }}>Low</option>
                                </select>
                                @error('urgency_level')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="issue_description" class="form-label fw-semibold text-dark">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('issue_description') is-invalid @enderror" 
                                    id="issue_description" name="issue_description" rows="5" required
                                    placeholder="Please describe the issue in detail...">{{ old('issue_description', $issue->issue_description) }}</textarea>
                                @error('issue_description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                          <!-- Attachments Section -->
<div class="mb-4">
    <label for="attachments" class="form-label fw-bold">Update Attachments</label>
    <input class="form-control @error('attachments.*') is-invalid @enderror" 
        type="file" id="attachments" name="attachments[]" multiple>
    @error('attachments.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Uploading new files will replace all existing attachments. Max 2MB each.</small>
    
    @if($issue->attachments->count() > 0)
        <div class="mt-3">
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
                        <span class="badge bg-light text-dark"> {{ round($attachment->file_size / 1024, 1) }} KB</span>
                    </div>
                @endforeach
            </div>
            <div class="alert alert-warning mt-2 p-2 small">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Uploading new files will delete all existing attachments above.
            </div>
        </div>
    @endif
</div>
                            <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-3">
                                <a href="{{ route('Student.issue_details', $issue->issue_id) }}" 
                                   class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .file-upload-wrapper {
        transition: all 0.3s ease;
    }
    .file-upload-wrapper:hover {
        background-color: #f8f9fa !important;
    }
    .form-select-lg {
        padding: 0.75rem 1rem;
    }
    textarea {
        min-height: 150px;
    }
</style>
@endpush