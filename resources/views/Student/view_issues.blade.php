@extends('Layouts.StudentNavbar')
@section('title', 'View All Issues')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Completed Issues</h1>
            <p class="text-muted mb-0">View All Completed Tasks</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm border-0 rounded-3 mb-4 hover-lift">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" id="issueSearch" class="form-control border-start-0 ps-0" placeholder="Search by issue type or location...">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <select class="form-select form-select-lg" id="urgencyFilter">
                        <option value="">All Urgency Levels</option>
                        <option value="Low">Low Priority</option>
                        <option value="Medium">Medium Priority</option>
                        <option value="High">High Priority</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Issues List -->
    @if($issues->count() > 0)
        <div class="card shadow-sm border-0 rounded-3 hover-lift">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Issue Type</th>
                            <th class="py-3">Urgency</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date Submitted</th>
                            <th class="py-3">Location</th>
                            <th class="text-end pe-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="issuesTableBody">
                        @foreach ($issues as $issue)
                            <tr class="border-bottom">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @php
                                            $iconClass = match($issue->issue_type) {
                                                'Plumbing' => 'fa-faucet',
                                                'Electrical' => 'fa-bolt',
                                                'Furniture' => 'fa-chair',
                                                'HVAC' => 'fa-temperature-high',
                                                'Internet' => 'fa-wifi',
                                                'Cleaning' => 'fa-broom',
                                                default => 'fa-question-circle'
                                            };
                                        @endphp
                                        <div class="icon-circle bg-light-primary me-3">
                                            <i class="fas {{ $iconClass }} text-primary"></i>
                                        </div>
                                        <span class="fw-medium">{{ $issue->issue_type }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($issue->urgency_level == 'Low') bg-success-subtle text-success
                                        @elseif($issue->urgency_level == 'Medium') bg-warning-subtle text-warning
                                        @elseif($issue->urgency_level == 'High') bg-danger-subtle text-danger
                                        @endif px-3 py-2">
                                        {{ $issue->urgency_level }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($issue->issue_status == 'Open') bg-primary-subtle text-primary
                                        @elseif($issue->issue_status == 'In Progress') bg-warning-subtle text-warning
                                        @elseif($issue->issue_status == 'Resolved') bg-success-subtle text-success
                                        @endif px-3 py-2">
                                        {{ $issue->issue_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt text-muted me-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($issue->report_date)->format('M d, Y') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-light-secondary me-2">
                                            <i class="fas fa-map-marker-alt text-secondary"></i>
                                        </div>
                                        <span>{{ $issue->building->building_name ?? 'unknown' }}</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('Student.issue_details', $issue->issue_id) }}"
                                       class="btn btn-primary btn-sm px-3">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $issues->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="card shadow-sm border-0 rounded-3 hover-lift">
            <div class="card-body py-5">
                <div class="text-center">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-clipboard-list fa-4x text-primary-subtle"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Completed Issues Found</h4>
                    <p class="text-muted mb-4">You currently don't have any completed maintenance issues.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.08);
}

.icon-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-light-primary {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.bg-light-secondary {
    background-color: rgba(var(--bs-secondary-rgb), 0.1);
}

.input-group-merge .input-group-text {
    border-right: 0;
}

.input-group-merge .form-control:focus {
    border-color: #dee2e6;
    box-shadow: none;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 50%;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.badge {
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('issueSearch');
    const urgencyFilter = document.getElementById('urgencyFilter');
    const tableBody = document.getElementById('issuesTableBody');

    function filterIssues() {
        const searchTerm = searchInput.value.toLowerCase();
        const urgencyValue = urgencyFilter.value;
        const rows = tableBody.getElementsByTagName('tr');

        Array.from(rows).forEach(row => {
            const issueType = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const urgency = row.querySelector('td:nth-child(2)').textContent.trim();
            const location = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

            const matchesSearch = issueType.includes(searchTerm) || location.includes(searchTerm);
            const matchesUrgency = !urgencyValue || urgency.includes(urgencyValue);

            row.style.display = matchesSearch && matchesUrgency ? '' : 'none';
        });

        // Show/hide empty state
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        const emptyState = document.querySelector('.empty-state');
        if (emptyState) {
            emptyState.style.display = visibleRows.length === 0 ? 'block' : 'none';
        }
    }

    // Add debounce to search
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterIssues, 300);
    });

    urgencyFilter.addEventListener('change', filterIssues);

    // Initialize tooltips if using Bootstrap 5
    if (typeof bootstrap !== 'undefined') {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    }
});
</script>
@endsection
