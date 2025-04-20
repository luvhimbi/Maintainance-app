@extends('layouts.StudentNavbar')

@section('title', 'Edit Issue')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h3 class="mb-0 fw-bold">Edit Issue #{{ $issue->issue_id }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('Student.updateissue', $issue->issue_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Location Selection -->
                            <div class="mb-4">
                                <label for="location_id" class="form-label fw-bold">Location</label>
                                <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                    <option value="">Select a location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}" 
                                            {{ $issue->location_id == $location->location_id ? 'selected' : '' }}>
                                            {{ $location->building_name }}, Room {{ $location->room_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Issue Type -->
                            <div class="mb-4">
                                <label for="issue_type" class="form-label fw-bold">Issue Type</label>
                                <select class="form-select @error('issue_type') is-invalid @enderror" id="issue_type" name="issue_type" required>
                                    <option value="">Select issue type</option>
                                    <option value="Plumbing" {{ $issue->issue_type == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
                                    <option value="Electrical" {{ $issue->issue_type == 'Electrical' ? 'selected' : '' }}>Electrical</option>
                                    <option value="Furniture" {{ $issue->issue_type == 'Structural' ? 'selected' : '' }}>Furniture</option>
                                   
                                </select>
                                @error('issue_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Urgency Level -->
                            <div class="mb-4">
                                <label for="urgency_level" class="form-label fw-bold">Urgency Level</label>
                                <select class="form-select @error('urgency_level') is-invalid @enderror" id="urgency_level" name="urgency_level" required>
                                    <option value="">Select urgency level</option>
                                    <option value="High" {{ $issue->urgency_level == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Medium" {{ $issue->urgency_level == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Low" {{ $issue->urgency_level == 'Low' ? 'selected' : '' }}>Low</option>
                                </select>
                                @error('urgency_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="issue_description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control @error('issue_description') is-invalid @enderror" 
                                    id="issue_description" name="issue_description" rows="5" required>{{ old('issue_description', $issue->issue_description) }}</textarea>
                                @error('issue_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

<!-- In your edit form (editissue.blade.php) -->
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
                            <a href="{{ Storage::disk($attachment->storage_disk)->url($attachment->file_path) }}" 
                               target="_blank" class="text-decoration-none">
                                {{ $attachment->original_name }}
                            </a>
                        </div>
                        <span class="text-muted small">{{ $attachment->file_size }} bytes</span>
                    </div>
                @endforeach
            </div>
            <div class="form-text mt-2">
                <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                Uploading new files will delete all existing attachments above.
            </div>
        </div>
    @endif
</div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('Student.issue_details', $issue->issue_id) }}" 
                                   class="btn btn-outline-secondary me-md-2 px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Update Issue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection