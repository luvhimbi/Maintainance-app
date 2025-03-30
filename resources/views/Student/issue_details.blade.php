@extends('layouts.StudentNavbar')
@section('title', 'Issue Details')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-2 mb-md-0">Issue Details</h1>
        <a href="{{ route('Student.view_issues') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to All Issues
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
                            {{ $issue->location->building_name }}, Room {{ $issue->location->room_number }}
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
                                        {{ substr($issue->task->assignee->username, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold">{{ $issue->task->assignee->username }}</p>
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
    <!-- Comment Section -->
    <div class="mt-4">
        <h5>Comments</h5>

        <!-- Add Comment Form -->
        @auth
            <form action="{{ route('issue.comment.store', $issue->issue_id) }}" method="POST" class="mb-4">
                @csrf
                <div class="form-group">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Submit
                </button>
            </form>
        @else
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>Please <a href="{{ route('login') }}">login</a> to post a comment
            </div>
        @endauth

        <!-- Display Comments -->
        @if ($issue->comments->count() > 0)
            <div class="list-group">
                @foreach ($issue->comments as $comment)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $comment->user->username }}</strong>
                                <span class="badge bg-secondary">{{ $comment->user->user_role }}</span>
                            </div>
                            <small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                        <p class="mb-0">{{ $comment->comment }}</p>

                        <!-- Edit and Delete Buttons (only for the comment owner) -->
                        @auth
                            @if (auth()->id() === $comment->user_id)
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary edit-comment-btn" data-comment-id="{{ $comment->id }}" data-comment-text="{{ $comment->comment }}">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                    <form action="{{ route('comment.delete', $comment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this comment?')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        @else
            <p>No comments yet.</p>
        @endif
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle edit button clicks
        document.querySelectorAll('.edit-comment-btn').forEach(button => {
            button.addEventListener('click', function () {
                const commentId = this.getAttribute('data-comment-id');
                const commentText = this.getAttribute('data-comment-text');

                // Create a form for editing
                const editForm = `
                    <form action="/comment/${commentId}" method="POST" class="mt-2">
                        @csrf
                        @method('PUT')
                        <textarea name="comment" class="form-control mb-2" rows="3" required>${commentText}</textarea>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save me-1"></i>Save
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-edit-btn">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                    </form>
                `;

                // Replace the comment text with the edit form
                this.closest('.list-group-item').querySelector('p').innerHTML = editForm;

                // Handle cancel button clicks
                const cancelButton = this.closest('.list-group-item').querySelector('.cancel-edit-btn');
                if (cancelButton) {
                    cancelButton.addEventListener('click', function () {
                        // Restore the original comment text
                        this.closest('.list-group-item').querySelector('p').textContent = commentText;
                    });
                }
            });
        });
    });
</script>
