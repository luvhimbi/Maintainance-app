@extends('layouts.AdminNavBar')
@section('title', 'Feedback Management')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Feedback Management</h1>
        <div>
            <a href="{{ route('admin.feedbacks.export') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export to Excel
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Feedback</h6>
            <div class="input-group" style="width: 300px;">
                <input type="text" id="feedbackSearch" class="form-control" placeholder="Search feedback...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="feedbackTableContainer">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0" id="feedbackTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Issue</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Comments</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedbacks as $feedback)
                            <tr class="feedback-row" 
                                data-id="{{ $feedback->id }}"
                                data-issue="{{ $feedback->issue->issue_type ?? '' }}"
                                data-user="{{ $feedback->user->first_name ?? '' }}"
                                data-rating="{{ $feedback->rating }}"
                                data-comments="{{ $feedback->comments ?? '' }}"
                                data-date="{{ $feedback->created_at->format('M d, Y H:i') }}">
                                <td>{{ $feedback->id }}</td>
                                <td>
                                    <a>
                                        {{ Str::limit($feedback->issue->issue_type ?? 'N/A', 30) }}
                                    </a>
                                </td>
                                <td>{{ $feedback->user->first_name ?? 'N/A' }}</td>
                                <td class="rating-cell">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $feedback->rating ? ' text-warning' : ' text-secondary' }}"></i>
                                    @endfor
                                </td>
                                <td>{{ $feedback->comments ? Str::limit($feedback->comments, 50) : 'N/A' }}</td>
                                <td>{{ $feedback->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No feedback found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $feedbacks->links() }}
                </div>
            </div>
            <div id="noResultsMessage" class="alert alert-warning" style="display: none;">
                No feedback matches your search criteria.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('feedbackSearch');
    const searchButton = document.getElementById('searchButton');
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
            
            // Check if search term matches any field
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

        // Show/hide no results message
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

    // Event listeners
    searchButton.addEventListener('click', performSearch);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Initial search in case there's a value already (e.g., from page refresh)
    if (searchInput.value) {
        performSearch();
    }
});
</script>
@endpush

@endsection