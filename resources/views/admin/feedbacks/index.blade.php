@extends('layouts.AdminNavBar')
@section('title', 'Feedback Management')
@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h2 class="h5 mb-1 fw-bold text-dark">Feedback Management</h2>
                <p class="text-muted small mb-0">View and manage all feedback submitted by users</p>
            </div>
            <form class="d-flex mt-3 mt-md-0 align-items-center" style="max-width: 400px;">
                <div class="input-group rounded-pill overflow-hidden shadow-sm-sm w-100">
                    <span class="input-group-text bg-light border-0 ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="feedbackSearch" class="form-control border-0 pe-3" placeholder="Search feedback...">
                    <button class="btn btn-outline-secondary border-0" type="button" id="clearSearch" aria-label="Clear search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div id="feedbackTableContainer">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="feedbackTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th class="py-3">Issue</th>
                                <th class="py-3">User</th>
                                <th class="py-3">Rating</th>
                                <th class="py-3">Comments</th>
                                <th class="py-3">Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedbacks as $feedback)
                            <tr class="feedback-row border-top"
                                data-id="{{ $feedback->id }}"
                                data-issue="{{ $feedback->issue->issue_type ?? '' }}"
                                data-user="{{ $feedback->user->first_name ?? '' }}"
                                data-rating="{{ $feedback->rating }}"
                                data-comments="{{ $feedback->comments ?? '' }}"
                                data-date="{{ $feedback->created_at->format('M d, Y H:i') }}">
                                <td class="ps-4 fw-bold text-dark">{{ $feedback->id }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-medium">
                                        {{ Str::limit($feedback->issue->issue_type ?? 'N/A', 30) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $feedback->user->first_name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $feedback->rating ? ' text-warning' : ' text-secondary' }}"></i>
                                        @endfor
                                        <span class="ms-2 small text-muted">{{ $feedback->rating }}/5</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $feedback->comments ? Str::limit($feedback->comments, 50) : 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $feedback->created_at->format('M d, Y H:i') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <h5 class="text-muted">No feedback found</h5>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $feedbacks->firstItem() ?? 0 }} to {{ $feedbacks->lastItem() ?? 0 }} of {{ $feedbacks->total() ?? 0 }} entries
                    </div>
                    <div>
                        {{ $feedbacks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            <div id="noResultsMessage" class="alert alert-warning rounded-3 mt-4 mx-4" style="display: none;">
                <i class="fas fa-exclamation-circle me-2"></i>
                No feedback matches your search criteria.
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Poppins', sans-serif;
    }
    .card {
        border: 1px solid #e0e0e0;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
    }
    .card-header {
        background-color: #ffffff;
        color: #343a40;
        border-bottom: 1px solid #e9ecef;
    }
    .card-header h2, .card-header p {
        color: #343a40 !important;
    }
    .table thead th {
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
    }
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #f0f2f5;
    }
    .badge {
        font-weight: 500;
        font-size: 0.95em;
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .text-primary { color: #0d6efd !important; }
    .rounded-4 { border-radius: 1.25rem !important; }
    .rounded-3 { border-radius: 0.75rem !important; }
    .shadow-sm { box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.05) !important; }
    .input-group.rounded-pill {
        border: 1px solid #ced4da;
        border-radius: 2rem;
        overflow: hidden;
    }
    .input-group.rounded-pill .form-control,
    .input-group.rounded-pill .input-group-text,
    .input-group.rounded-pill .btn {
        border: none !important;
        background-color: white;
    }
    .input-group.rounded-pill .form-control:focus {
        box-shadow: none;
    }
    .input-group.rounded-pill .input-group-text {
        padding-left: 1rem;
    }
    .input-group.rounded-pill .form-control {
        padding-left: 0.5rem;
    }
    .input-group.rounded-pill .btn {
        padding-right: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('feedbackSearch');
    const clearButton = document.getElementById('clearSearch');
    const feedbackRows = document.querySelectorAll('.feedback-row');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const feedbackTable = document.getElementById('feedbackTable');
    const paginationContainer = document.querySelector('.pagination');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        let hasMatches = false;

        feedbackRows.forEach(row => {
            const issue = row.getAttribute('data-issue').toLowerCase();
            const user = row.getAttribute('data-user').toLowerCase();
            const comments = row.getAttribute('data-comments').toLowerCase();
            const rating = row.getAttribute('data-rating');
            const matches = issue.includes(searchTerm) ||
                           user.includes(searchTerm) ||
                           comments.includes(searchTerm) ||
                           rating.includes(searchTerm);

            if (matches || searchTerm === '') {
                row.style.display = '';
                hasMatches = true;
            } else {
                row.style.display = 'none';
            }
        });

        if (!hasMatches && searchTerm !== '') {
            noResultsMessage.style.display = 'block';
            feedbackTable.style.display = 'none';
            if (paginationContainer) paginationContainer.style.display = 'none';
        } else {
            noResultsMessage.style.display = 'none';
            feedbackTable.style.display = '';
            if (paginationContainer) paginationContainer.style.display = 'flex';
        }
    }

    // Search on input
    searchInput.addEventListener('keyup', function(e) {
        performSearch();
    });

    // Clear search input
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            performSearch();
        });
    }

    // Initial search in case there's a value already
    if (searchInput.value) {
        performSearch();
    }
});
</script>
@endpush

@endsection