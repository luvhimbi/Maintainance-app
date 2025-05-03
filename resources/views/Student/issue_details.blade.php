@extends('layouts.StudentNavbar')
@section('title', 'Issue Details')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-2 mb-md-0">Issue Details</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to  dashboard?
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-light py-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2 text-primary"></i>{{ $issue->issue_type }}
                    </h4>
                </div>
                <div class="col-12 col-md-4 mt-2 mt-md-0 text-md-end">
                    <span class="me-2"><i class="fas fa-calendar-alt me-1"></i> Reported: {{ \Carbon\Carbon::parse($issue->report_date)->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-4 mb-md-0">
                    <div class="mb-4">
                        <h5 class="text-muted fs-6">Location</h5>
                        <p class="card-text fs-5">
                            <i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                            {{ $issue->location->building_name ?? 'unknown building name'}}, Room {{ $issue->location->room_number ??'unknown room no' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-muted fs-6">Description</h5>
                        <p class="card-text">{{ $issue->issue_description }}</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Status</h5>
                            <div>
                                @if($issue->issue_status == 'Open')
                                    <span class="badge bg-primary">Open</span>
                                @elseif($issue->issue_status == 'In Progress')
                                    <span class="badge bg-warning text-dark">In Progress</span>
                                @elseif($issue->issue_status == 'Resolved')
                                    <span class="badge bg-success">Resolved</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Urgency</h5>
                            <div>
                                @if($issue->urgency_level == 'Low')
                                    <span class="badge bg-success">Low</span>
                                @elseif($issue->urgency_level == 'Medium')
                                    <span class="badge bg-warning text-dark">Medium</span>
                                @elseif($issue->urgency_level == 'High')
                                    <span class="badge bg-danger">High</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0 rounded-3 mb-3">
                        <div class="card-body">
                            <h5 class="card-title fs-6">
                                <i class="fas fa-user-cog me-2"></i>Assigned Technician
                            </h5>
                            @if ($issue->task && $issue->task->assignee)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        {{ substr($issue->task->assignee->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold">{{ $issue->task->assignee->first_name }}</p>
                                    </div>
                                </div>
                                <div class="ms-1 ps-1 border-start">
                                    <p class="mb-1 small">
                                        <i class="fas fa-envelope me-2 text-secondary"></i>{{ $issue->task->assignee->email }}
                                    </p>
                                    <p class="mb-0 small">
                                        <i class="fas fa-phone me-2 text-secondary"></i>{{ $issue->task->assignee->phone_number }}
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-secondary py-2 mb-0">
                                    <small><i class="fas fa-info-circle me-1"></i>No technician assigned yet</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="mb-3">
                <i class="fas fa-paperclip me-2"></i>Attachments
            </h5>
            @if ($issue->attachments->count() > 0)
                <div class="row g-2">
                    @foreach ($issue->attachments as $attachment)
                        @php
                            $fileExtension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);
                            $iconClass = 'fa-file';

                            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $iconClass = 'fa-file-image';
                            } elseif (in_array($fileExtension, ['pdf'])) {
                                $iconClass = 'fa-file-pdf';
                            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                $iconClass = 'fa-file-word';
                            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                $iconClass = 'fa-file-excel';
                            }
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4">
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <i class="fas {{ $iconClass }} fa-2x me-3 text-secondary"></i>
                                        <div class="text-truncate">{{ $attachment->original_name }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary" role="alert">
                    <i class="fas fa-info-circle me-2"></i>No attachments found
                </div>
            @endif
        </div>
    </div>

    <!-- Task Updates Section -->
   <div class="mt-5">
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
            <i class="fas fa-tasks fs-4 text-primary"></i>
        </div>
        <h4 class="mb-0 text-dark">Task Updates</h4>
    </div>

    @if ($issue->task && $issue->task->updates->count() > 0)
        <div class="timeline ps-3">
            @foreach ($issue->task->updates->sortByDesc('update_timestamp') as $update)
                <div class="timeline-item position-relative pb-4">
                    <div class="timeline-badge position-absolute top-0 start-0 translate-middle rounded-circle d-flex align-items-center justify-content-center bg-white border border-3
                        @if($update->status_change == 'In Progress') border-warning
                        @elseif($update->status_change == 'Resolved') border-success
                        @else border-primary @endif"
                        style="width: 24px; height: 24px;">
                        <i class="fas fa-circle fs-6
                            @if($update->status_change == 'In Progress') text-warning
                            @elseif($update->status_change == 'Resolved') text-success
                            @else text-primary @endif"></i>
                    </div>

                    <div class="timeline-content ms-5 ps-3">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center">
                                        @if($update->staff)
                                            <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px; font-size: 1.1rem;">
                                                {{ substr($update->staff->first_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark">{{ $update->staff->first_name}}</h6>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $update->staff->user_role }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small">[Staff removed]</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small">
                                        {{ \Carbon\Carbon::parse($update->update_timestamp)->format('M j, Y · g:i A') }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <span class="badge rounded-pill
                                        @if($update->status_change == 'In Progress') bg-warning bg-opacity-10 text-warning
                                        @elseif($update->status_change == 'Resolved') bg-success bg-opacity-10 text-success
                                        @else bg-primary bg-opacity-10 text-primary @endif">
                                        <i class="fas
                                            @if($update->status_change == 'In Progress') fa-wrench me-1
                                            @elseif($update->status_change == 'Resolved') fa-check me-1
                                            @else fa-info-circle me-1 @endif"></i>
                                        {{ $update->status_change }}
                                    </span>
                                </div>

                                <p class="mb-0 text-dark">{{ $update->update_description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 bg-light">
            <div class="card-body text-center py-4">
                <i class="fas fa-info-circle text-primary fs-4 mb-3"></i>
                <h5 class="text-dark">No updates yet</h5>
                <p class="text-muted mb-0">Task updates will appear here once available</p>
            </div>
        </div>
    @endif
</div>

</div>
@if($issue->issue_status == 'Resolved' && !$issue->hasFeedbackFrom(auth()->user()))
<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="feedbackModalLabel">Provide Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="feedbackForm" action="{{ route('feedback.submit', $issue->issue_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4 text-center">
                        <h6>How would you rate the resolution of this issue?</h6>
                        <div class="rating-stars mt-3">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $i == 5 ? 'checked' : '' }}>
                                <label for="star{{ $i }}" title="{{ $i }} star"><i class="fas fa-star"></i></label>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comments" class="form-label">Additional comments (optional)</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Auto-show modal if conditions are met
    document.addEventListener('DOMContentLoaded', function() {
        @if($issue->issue_status == 'Resolved' && !$issue->hasFeedbackFrom(auth()->user()))
            var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
            feedbackModal.show();
        @endif
    });
    @if(session('swal_error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('swal_error') }}',
        confirmButtonColor: '#4361ee',
    });

  @endif
</script>
@endpush


@endsection

@push('styles')
<style>
     .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 12px;
        width: 2px;
        background-color: #e9ecef;
    }
    .timeline-item:last-child {
        padding-bottom: 0 !important;
    }
    /* Rating stars styling */
    .rating-stars {
        display: inline-block;
        direction: rtl; /* Right to left */
        unicode-bidi: bidi-override;
    }
    .rating-stars input {
        display: none;
    }
    .rating-stars label {
        color: #ddd;
        font-size: 2rem;
        padding: 0 5px;
        cursor: pointer;
    }
    .rating-stars input:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #ffc107; /* Gold/yellow color */
    }
    .rating-stars input:checked + label:hover,
    .rating-stars input:checked ~ label:hover,
    .rating-stars label:hover ~ input:checked ~ label,
    .rating-stars input:checked ~ label:hover ~ label {
        color: #ffc107;
    }
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-badge {
        position: absolute;
        left: -20px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
    }
    .timeline-content {
        position: relative;
        margin-left: 20px;
        border-left: 3px solid #dee2e6;
        padding-left: 20px;
    }
    .timeline-item:last-child .timeline-content {
        border-left-color: transparent;
    }
    .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .symbol-35px {
        width: 35px;
        height: 35px;
    }
    .symbol-circle {
        border-radius: 50%;
    }
    .symbol-label {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        width: 100%;
        height: 100%;
    }
</style>
@endpush
