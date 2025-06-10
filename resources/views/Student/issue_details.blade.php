@extends('layouts.StudentNavbar')
@section('title', 'Issue Details')
@section('content')
    <div class="container mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-2 mb-md-0">Issue Details</h1>
            <a href="{{ route('Student.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                <i class="fas fa-arrow-left me-1"></i> Back to dashboard
            </a>
        </div>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-light py-3 px-4 border-bottom rounded-top-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-exclamation-circle text-primary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold text-dark">{{ $issue->issue_type }}</h4>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                {{ $issue->location->building_name ?? 'Unknown' }}, Room {{ $issue->location->room_number ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                <span class="badge rounded-pill me-2 py-2 px-3 fw-medium
    @if($issue->urgency_level == 'High') bg-danger-subtle text-danger
    @elseif($issue->urgency_level == 'Medium') bg-warning-subtle text-warning
    @else bg-success-subtle text-success
    @endif">
    {{ $issue->urgency_level }} Priority ({{ $issue->urgency_score }})
</span>

                        <span class="text-muted small">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ \Carbon\Carbon::parse($issue->report_date)->format('M d, Y') }}
                </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-align-left"></i>
                        </span>
                                Issue Description
                            </h5>
                            <div class="ps-4 ms-3 border-start border-2 border-light">
                                <p class="card-text text-dark">{{ $issue->issue_description }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-info-circle"></i>
                        </span>
                                Issue Details
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center bg-light rounded-3 p-3 h-100">
                                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div>
                                            <p class="small text-muted mb-0">Status</p>
                                            <p class="mb-0 fw-bold">
                                                @php
                                                    $statusBadgeClass = '';
                                                    $statusIconClass = '';
                                                    switch($issue->issue_status) {
                                                        case 'Open':
                                                            $statusBadgeClass = 'bg-info-subtle text-info';
                                                            $statusIconClass = 'fa-folder-open';
                                                            break;
                                                        case 'In Progress':
                                                            $statusBadgeClass = 'bg-primary-subtle text-primary';
                                                            $statusIconClass = 'fa-spinner fa-spin';
                                                            break;
                                                        case 'Resolved':
                                                            $statusBadgeClass = 'bg-success-subtle text-success';
                                                            $statusIconClass = 'fa-check-circle';
                                                            break;
                                                        case 'Closed':
                                                            $statusBadgeClass = 'bg-secondary-subtle text-secondary';
                                                            $statusIconClass = 'fa-times-circle';
                                                            break;
                                                        default:
                                                            $statusBadgeClass = 'bg-secondary-subtle text-secondary';
                                                            $statusIconClass = 'fa-question-circle';
                                                            break;
                                                    }
                                                @endphp
                                                <span class="badge rounded-pill {{ $statusBadgeClass }} py-2 px-3 fw-medium">
                                            <i class="fas {{ $statusIconClass }} me-1"></i> {{ $issue->issue_status }}
                                        </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center bg-light rounded-3 p-3 h-100">
                                        <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div>
                                            <p class="small text-muted mb-0">Safety Hazard</p>
                                            <p class="mb-0 fw-bold text-dark">
                                                {{ $issue->safety_hazard ? 'Yes' : 'No' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center bg-light rounded-3 p-3 h-100">
                                        <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div>
                                            <p class="small text-muted mb-0">Affects Operations</p>
                                            <p class="mb-0 fw-bold text-dark">
                                                {{ $issue->affects_operations ? 'Yes' : 'No' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-center bg-light rounded-3 p-3 h-100">
                                        <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-layer-group"></i>
                                        </div>
                                        <div>
                                            <p class="small text-muted mb-0">Affected Areas</p>
                                            <p class="mb-0 fw-bold text-dark">{{ $issue->affected_areas }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($issue->issue_type == 'PC')
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center bg-light rounded-3 p-3 h-100">
                                            <div class="bg-purple bg-opacity-10 text-purple p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-desktop"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-0">PC Number</p>
                                                <p class="mb-0 fw-bold text-dark">{{ $issue->pc_number ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center bg-light rounded-3 p-3 h-100">
                                            <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-briefcase"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-0">Critical Work Affected</p>
                                                <p class="mb-0 fw-bold text-dark">
                                                    {{ $issue->critical_work_affected ? 'Yes' : 'No' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center bg-light rounded-3 p-3 h-100">
                                            <div class="bg-purple bg-opacity-10 text-purple p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-tools"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-0">PC Issue Type</p>
                                                <p class="mb-0 fw-bold text-dark">{{ ucfirst($issue->pc_issue_type ?? 'N/A') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold d-flex align-items-center mb-3 text-dark">
                            <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-user-cog"></i>
                            </span>
                                    Technician Details
                                </h5>

                                @if ($issue->task && $issue->task->assignee)
                                    <div class="text-center mb-3">
                                        <div class="avatar avatar-xl bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 80px; height: 80px;">
                                    <span class="fs-3 fw-bold">
                                        {{ substr($issue->task->assignee->first_name, 0, 1) }}{{ substr($issue->task->assignee->last_name, 0, 1) }}
                                    </span>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $issue->task->assignee->first_name }} {{$issue->task->assignee->last_name}}</h6>
                                        <p class="text-muted small mb-2">Assigned Technician</p>
                                    </div>

                                    <div class="border-top pt-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-light p-2 rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-envelope text-muted"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-0">Email</p>
                                                <a href="mailto:{{ $issue->task->assignee->email }}" class="text-decoration-none">
                                                    <p class="mb-0 fw-bold text-dark">{{ $issue->task->assignee->email }}</p>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-phone text-muted"></i>
                                            </div>
                                            <div>
                                                <p class="small text-muted mb-0">Phone</p>
                                                <a href="tel:{{ $issue->task->assignee->phone_number }}" class="text-decoration-none">
                                                    <p class="mb-0 fw-bold text-dark">{{ $issue->task->assignee->phone_number }}</p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-light border d-flex align-items-center rounded-3" role="alert">
                                        <i class="fas fa-info-circle text-secondary me-2"></i>
                                        <div>
                                            <small class="text-muted">No technician has been assigned to this issue yet.</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top border-light">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                <span class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-paperclip"></i>
                </span>
                        Attachments
                    </h5>

                    @if ($issue->attachments->count() > 0)
                        <div class="row g-3">
                            @foreach ($issue->attachments as $attachment)
                                @php
                                    $fileExtension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);
                                    $iconClass = 'fa-file';
                                    $bgClass = 'bg-secondary-subtle text-secondary'; // Default for unknown types

                                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                        $iconClass = 'fa-file-image';
                                        $bgClass = 'bg-success-subtle text-success';
                                    } elseif (in_array($fileExtension, ['pdf'])) {
                                        $iconClass = 'fa-file-pdf';
                                        $bgClass = 'bg-danger-subtle text-danger';
                                    } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                        $iconClass = 'fa-file-word';
                                        $bgClass = 'bg-primary-subtle text-primary';
                                    } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                        $iconClass = 'fa-file-excel';
                                        $bgClass = 'bg-success-subtle text-success';
                                    } elseif (in_array($fileExtension, ['mp4', 'mov', 'avi'])) {
                                        $iconClass = 'fa-file-video';
                                        $bgClass = 'bg-info-subtle text-info';
                                    }
                                @endphp
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                        <div class="card border shadow-sm h-100 rounded-3 hover-shadow">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="{{ $bgClass }} p-3 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="fas {{ $iconClass }} fa-lg"></i>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="mb-0 fw-bold text-truncate text-dark">{{ $attachment->original_name }}</p>
                                                        <p class="small text-muted mb-0 text-uppercase">{{ $fileExtension }} file</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border d-flex align-items-center rounded-3 py-3 px-4" role="alert">
                            <i class="fas fa-info-circle text-secondary me-2"></i>
                            <div>
                                <small class="text-muted">No attachments available for this issue.</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-tasks fs-4 text-primary"></i>
                </div>
                <h4 class="mb-0 text-dark fw-bold">Task Updates</h4>
            </div>

            @if ($issue->task && $issue->task->updates->count() > 0)
                <div class="timeline ps-3">
                    @foreach ($issue->task->updates->sortByDesc('update_timestamp') as $update)
                        <div class="timeline-item position-relative pb-4">
                            <div class="timeline-badge position-absolute top-0 start-0 translate-middle rounded-circle d-flex align-items-center justify-content-center bg-white border border-3
                        @if($update->status_change == 'In Progress') border-primary
                        @elseif($update->status_change == 'Resolved') border-success
                        @else border-info @endif"
                                 style="width: 24px; height: 24px;">
                                <i class="fas fa-circle fs-6
                            @if($update->status_change == 'In Progress') text-primary
                            @elseif($update->status_change == 'Resolved') text-success
                            @else text-info @endif"></i>
                            </div>

                            <div class="timeline-content ms-5 ps-3">
                                <div class="card border-0 shadow-sm mb-3 rounded-3">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                @if($update->staff)
                                                    <div class="bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3"
                                                         style="width: 40px; height: 40px; font-size: 1.1rem;">
                                                        {{ substr($update->staff->first_name, 0, 1) }}{{ substr($update->staff->last_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark fw-semibold">{{ $update->staff->first_name}} {{ $update->staff->last_name}}</h6>
                                                        <span class="badge bg-secondary-subtle text-secondary small fw-medium">{{ $update->staff->user_role }}</span>
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
                                    <span class="badge rounded-pill py-2 px-3 fw-medium
                                        @if($update->status_change == 'In Progress') bg-primary-subtle text-primary
                                        @elseif($update->status_change == 'Resolved') bg-success-subtle text-success
                                        @else bg-info-subtle text-info @endif">
                                        <i class="fas
                                            @if($update->status_change == 'In Progress') fa-spinner fa-spin me-1
                                            @elseif($update->status_change == 'Resolved') fa-check-circle me-1
                                            @else fa-info-circle me-1 @endif"></i>
                                        Status: {{ $update->status_change }}
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
                <div class="card border-0 bg-light rounded-3">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-info-circle text-primary fs-4 mb-3"></i>
                        <h5 class="text-muted fw-semibold">No updates yet</h5>
                        <p class="text-muted mb-0">Task updates will appear here once available.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Feedback Modal (Bootstrap) --}}
        <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title fw-bold text-dark" id="feedbackModalLabel">
                            <i class="fas fa-comment-dots me-2 text-primary"></i>Provide Feedback
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="feedbackFormBootstrap">
                        <div class="modal-body">
                            <div class="mb-4 text-center">
                                <h6 class="fw-bold text-dark">How would you rate the resolution of this issue?</h6>
                                <div class="rating-stars mt-3 d-flex justify-content-center flex-row-reverse">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="d-none" {{ $i == 5 ? 'checked' : '' }}>
                                        <label for="star{{ $i }}" title="{{ $i }} star" class="star-label" style="font-size:2rem; color:#ddd; cursor:pointer;">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                <p class="mt-2 fw-bold text-primary" id="ratingValue">5 Stars</p>
                            </div>
                            <div class="mb-3">
                                <label for="comments" class="form-label fw-bold text-dark">Additional Comments (Optional)</label>
                                <textarea class="form-control rounded-3" id="comments" name="comments" rows="4" placeholder="Share your thoughts..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-paper-plane me-1"></i> Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
document.addEventListener('DOMContentLoaded', function() {
    // Show Bootstrap modal if feedback is needed
    @if($issue->issue_status == 'Resolved' && !$issue->hasFeedbackFrom(auth()->user()))
        var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
        feedbackModal.show();
    @endif

    // Star rating logic for Bootstrap modal
    function updateStars(rating) {
        document.querySelectorAll('.rating-stars label').forEach(function(label) {
            var starValue = parseInt(label.getAttribute('for').replace('star', ''));
            label.querySelector('i').style.color = (starValue <= rating) ? '#ffc107' : '#ddd';
        });
        document.getElementById('ratingValue').textContent = rating + (rating == 1 ? ' Star' : ' Stars');
    }

    // Set initial stars
    updateStars(5);

    // Listen for star click
    document.querySelectorAll('.rating-stars input').forEach(function(input) {
        input.addEventListener('change', function() {
            updateStars(parseInt(this.value));
        });
    });

    // Feedback form submission
    var feedbackForm = document.getElementById('feedbackFormBootstrap');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var checkedInput = document.querySelector('.rating-stars input:checked');
            var rating = checkedInput ? checkedInput.value : 5;
            var comments = document.getElementById('comments') ? document.getElementById('comments').value : '';
            // Get CSRF token from hidden input if available
            var csrfToken = '';
            var csrfInput = document.querySelector('input[name="_token"]');
            if (csrfInput) {
                csrfToken = csrfInput.value;
            } else {
                // fallback to meta tag
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
            }
            var submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            fetch("{{ route('feedback.submit', $issue->issue_id) }}", {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rating: rating,
                    comments: comments
                })
            })
            .then(async response => {
                if (submitBtn) submitBtn.disabled = false;
                // Try to parse JSON, but if not valid JSON, show a generic error
                let data = null;
                let text = await response.text();
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    throw new Error('Server returned an invalid response. Please contact support.');
                }
                if (!response.ok) {
                    throw new Error((data && data.message) ? data.message : 'Failed to submit feedback.');
                }
                return data;
            })
            .then(data => {
                var modalEl = document.getElementById('feedbackModal');
                if (modalEl) {
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Feedback Submitted!',
                    text: 'Thank you for your valuable feedback.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary px-4 py-2 rounded-pill' },
                    buttonsStyling: false
                }).then(() => { window.location.reload(); });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message,
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary px-4 py-2 rounded-pill' },
                    buttonsStyling: false
                });
            });
        });
    }
});
            </script>
        @endpush

        @push('styles')
            <style>
                /* General body and card styling */
                body {
                    background-color: #f8f9fa; /* Light grey background */
                    font-family: 'Inter', sans-serif; /* Consistent font */
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

                .h5.fw-bold.text-dark, .h6.text-uppercase.text-muted.fw-bold {
                    color: #343a40 !important; /* Ensure dark text for section titles */
                }
                .text-muted.small {
                    color: #6c757d !important; /* Consistent muted text */
                }

                /* Custom subtle badge colors */
                .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
                .text-primary { color: #0d6efd !important; }

                .bg-success-subtle { background-color: rgba(40, 167, 69, 0.1) !important; }
                .text-success { color: #28a745 !important; }

                .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
                .text-warning { color: #ffc107 !important; }

                .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
                .text-danger { color: #dc3545 !important; }

                .bg-info-subtle { background-color: rgba(23, 162, 184, 0.1) !important; }
                .text-info { color: #17a2b8 !important; }

                .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
                .text-secondary { color: #6c757d !important; }

                /* Custom purple for PC details */
                .bg-purple { background-color: #6f42c1; }
                .bg-purple-subtle { background-color: rgba(111, 66, 193, 0.1) !important; }
                .text-purple { color: #6f42c1 !important; }

                /* Buttons */
                .btn-primary {
                    background-color: #0d6efd;
                    border-color: #0d6efd;
                }
                .btn-primary:hover {
                    background-color: #0b5ed7;
                    border-color: #0a58ca;
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

                /* Hover effect for attachment cards */
                .hover-shadow:hover {
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
                    transform: translateY(-2px);
                    transition: all 0.2s ease-in-out;
                }

                /* Timeline specific styles */
                .timeline {
                    position: relative;
                    padding-left: 20px; /* Adjust as needed */
                }
                .timeline::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    left: 12px; /* Center with badge */
                    width: 2px;
                    background: #e9ecef;
                }
                .timeline-item {
                    position: relative;
                    padding-bottom: 30px;
                }
                .timeline-item:last-child {
                    padding-bottom: 0;
                }
                .timeline-badge {
                    position: absolute;
                    left: 0;
                    top: 0;
                    transform: translateX(-50%);
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background-color: white;
                    border: 3px solid;
                    z-index: 1;
                }
                .timeline-content {
                    margin-left: 30px; /* Space for badge and line */
                    padding-left: 15px;
                }
                .timeline-item:last-child .timeline-content {
                    border-left-color: transparent; /* Remove line for last item */
                }

                /* Rating stars styling for SweetAlert2 */
                .rating-stars {
                    display: inline-flex; /* Use flexbox for alignment */
                    direction: rtl; /* Right to left for star order */
                    unicode-bidi: bidi-override;
                    gap: 5px; /* Space between stars */
                }
                .rating-stars input {
                    display: none; /* Hide radio buttons */
                }
                .rating-stars label {
                    color: #ddd; /* Default grey color */
                    font-size: 2rem; /* Star size */
                    padding: 0 2px; /* Small padding around each star */
                    cursor: pointer;
                    transition: color 0.3s ease; /* Smooth color transition */
                }
                .rating-stars input:checked ~ label,
                .rating-stars label:hover,
                .rating-stars label:hover ~ label {
                    color: #ffc107; /* Gold/yellow color on hover/checked */
                }
                /* Ensure checked state persists on hover of subsequent stars */
                .rating-stars input:checked + label:hover,
                .rating-stars input:checked ~ label:hover,
                .rating-stars label:hover ~ input:checked ~ label,
                .rating-stars input:checked ~ label:hover ~ label {
                    color: #ffc107;
                }

                /* SweetAlert2 custom styling */
                .feedback-swal-popup {
                    background-color: #ffffff !important;
                    border-radius: 1rem !important; /* More rounded corners */
                    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important; /* Stronger shadow */
                }
                .feedback-swal-header {
                    padding: 1.5rem !important;
                    border-bottom: 1px solid #e9ecef !important;
                }
                .feedback-swal-title {
                    color: #343a40 !important;
                    font-weight: bold !important;
                }
                .feedback-swal-content {
                    padding: 1.5rem !important;
                    color: #343a40 !important;
                }
                .swal2-actions {
                    padding: 1rem 1.5rem !important;
                    border-top: 1px solid #e9ecef !important;
                    background-color: #f8f9fa !important; /* Light background for footer */
                }
            </style>
    @endpush
@endsection
