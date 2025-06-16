@extends('layouts.StudentNavbar')

@section('title', ' Dashboard')

@section('content')
    <div class="container py-4 bg-body-tertiary">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
            <div>
                <h1 class="fw-bold mb-1 h3">Hi, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h1>
                <p class="lead text-muted mb-0">Track and manage your reported maintenance issues</p>
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">
                <form id="searchForm" method="GET" action="{{ route('Student.dashboard') }}" class="search-form">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-7">
                            <div class="input-group input-group-lg search-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text"
                                       id="searchInput"
                                       name="search"
                                       class="form-control border-start-0"
                                       value="{{ request('search') }}"
                                       placeholder="Search by type, location, or description..."
                                       aria-label="Search issues">
                                <button class="btn btn-outline-secondary" type="button" id="clearButton" title="Clear">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-5 mt-3 mt-md-0">
                            <div class="status-filters d-flex flex-wrap gap-2 justify-content-md-end">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input status-filter" type="checkbox" value="open" id="openCheck" name="status[]" {{ in_array('open', (array)request('status', ['open', 'in progress'])) ? 'checked' : '' }}>
                                    <label class="form-check-label status-badge status-open" for="openCheck">
                                        <i class="fas fa-circle me-1"></i>Open
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input status-filter" type="checkbox" value="in progress" id="progressCheck" name="status[]" {{ in_array('in progress', (array)request('status', ['open', 'in progress'])) ? 'checked' : '' }}>
                                    <label class="form-check-label status-badge status-progress" for="progressCheck">
                                        <i class="fas fa-circle me-1"></i>In Progress
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-bold h5">Your Active Reported Issues</h3>
                <span class="text-muted small" id="issueCount">{{ $issues->total() }} issues found</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Type</th>
                                <th class="border-0">Location</th>
                                <th class="border-0">Description</th>
                                <th class="border-0">Urgency_Level</th>
                                <th class="border-0">Issue Status</th>
                                <th class="border-0">Technician Assigned</th>
                                <th class="border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="issuesContainer">
                            @forelse ($issues as $issue)
                            @php
                                $icon = match($issue->issue_type) {
                                    'Plumbing'   => 'fa-faucet-drip',
                                    'Electrical' => 'fa-bolt-lightning',
                                    'Furniture'  => 'fa-couch',
                                    'HVAC'       => 'fa-fan',
                                    'Internet'   => 'fa-network-wired',
                                    'Structural' => 'fa-building',
                                    'General'    => 'fa-toolbox',
                                    default      => 'fa-toolbox',
                                };
                                $urgencyColor = match($issue->urgency_level) {
                                    'High' => 'danger',
                                    'Medium' => 'warning',
                                    default => 'secondary'
                                };
                                $statusColor = match($issue->issue_status) {
                                    'Open' => 'primary',
                                    'In Progress' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr class="issue-item"
                            data-issue-type="{{ strtolower(trim($issue->issue_type)) }}"
                            data-location="{{ strtolower(trim($issue->building->building_name ?? '') . ' ' . trim($issue->floor->floor_number ?? '') . ' ' . trim($issue->room->room_number ?? '')) }}"
                            data-description="{{ strtolower(trim($issue->issue_description)) }}"
                            data-status="{{ strtolower(trim($issue->issue_status)) }}"
                            data-assignee="{{ strtolower($issue->task->assignee->first_name ?? '') . ' ' . strtolower($issue->task->assignee->last_name ?? '') }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-{{ $urgencyColor }}-subtle text-{{ $urgencyColor }} rounded-circle p-2 me-2">
                                        <i class="fas {{ $icon }}"></i>
                                    </span>
                                    <span>{{ $issue->issue_type }}</span>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt text-secondary me-1"></i>
                                {{ $issue->building->building_name ?? 'N/A' }},
                                Floor {{ $issue->floor->floor_number ?? 'N/A' }},
                                Room {{ $issue->room->room_number ?? 'N/A' }}
                            </td>
                            <td>
                                <i class="fas fa-align-left text-secondary me-1"></i>
                                {{ \Illuminate\Support\Str::limit($issue->issue_description, 50) }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $urgencyColor }}-subtle text-{{ $urgencyColor }} px-3 py-1 rounded-pill">
                                    {{ $issue->urgency_level }} Priority
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-1 rounded-pill">
                                    <i class="fas fa-circle me-1 small-status"></i>
                                    {{ $issue->issue_status }}
                                </span>
                            </td>
                            <td>
                                @if($issue->task && $issue->task->assignee)
                                    <i class="fas fa-user text-secondary me-1"></i>
                                    {{ $issue->task->assignee->first_name }} {{ $issue->task->assignee->last_name }}
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-user-slash text-secondary me-1"></i>
                                        Not assigned
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    @if($issue->issue_status === 'Open')
                                        <a href="{{ route('Student.editissue', $issue->issue_id) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-pencil-alt me-1"></i> Edit
                                        </a>
                                    @endif
                                    <a href="{{ route('Student.issue_details', $issue->issue_id) }}"
                                       class="btn btn-sm btn-primary">
                                        Details <i class="fas fa-chevron-right ms-1 small"></i>
                                    </a>
                                </div>
                            </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
                                    <h4 class="fw-semibold mb-2">No issues found</h4>
                                    <p class="text-muted mb-3">
                                        @if(request('search') || request('status'))
                                            No issues match your search or filter criteria.
                                        @else
                                            Ready to report your first maintenance issue?
                                        @endif
                                    </p>
                                    <a href="{{ route('Student.createissue') }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                                        <i class="fas fa-plus me-2"></i> Report An Issue
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($issues->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $issues->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center py-5 d-none" id="emptyState">
            <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
            <h4 class="fw-semibold mb-2">No issues found</h4>
            <p class="text-muted mb-3">Try adjusting your search or filters, or report a new one.</p>
            <a href="{{ route('Student.createissue') }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                <i class="fas fa-plus me-2"></i> Report An Issue
            </a>
        </div>
    </div>

    @push('styles')
        <style>
            body {

                background-color: #f8f9fa;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .

            .small-status {
                font-size: 0.65em; /* Slightly smaller dot */
                vertical-align: middle;
            }
            #searchInput:focus {
                border-color: var(--bs-primary);
                box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15); /* Use BS variables */
            }


            /* Responsive tweaks for issue card layout */
            @media (max-width: 767.98px) { /* Using Bootstrap's md breakpoint */


                /* Adjust spacing for stacked elements on mobile */
                .issue-card > div:not(:first-child):not(.issue-card-bar) {
                    margin-top: 0.75rem;
                    margin-left: 0 !important; /* Resetting ms-md-auto etc. */
                }
                .issue-card .ms-md-auto { /* Target specific spacing classes if needed */
                    margin-left: 0 !important;
                }
                .issue-card .text-md-end { /* Reset text alignment if needed */
                    text-align: left !important;
                }
                .issue-card .align-items-md-end {
                    align-items: flex-start !important;
                }
            }

            /* New search and filter styles */
            .search-group {
                box-shadow: 0 2px 6px rgba(0,0,0,0.05);
                border-radius: 0.75rem;
                overflow: hidden;
            }

            .search-group .input-group-text,
            .search-group .form-control {
                border-color: #e9ecef;
            }

            .search-group .form-control:focus {
                box-shadow: none;
                border-color: #e9ecef;
            }

            .status-filters {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1rem;
                border-radius: 2rem;
                font-size: 0.875rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                border: 1px solid transparent;
            }

            .status-open {
                background-color: rgba(13, 110, 253, 0.1);
                color: #0d6efd;
            }

            .status-progress {
                background-color: rgba(255, 193, 7, 0.1);
                color: #ffc107;
            }


            .status-badge:hover {
                opacity: 0.85;
            }

            .form-check-input:checked + .status-badge {
                border-color: currentColor;
                font-weight: 600;
            }

            .form-check-input {
                display: none;
            }

            .status-badge i {
                font-size: 0.5rem;
            }

            @media (max-width: 767.98px) {
                .status-filters {
                    justify-content: flex-start;
                }
            }

            .table > :not(caption) > * > * {
                padding: 1rem;
            }

            .table tbody tr {
                transition: all 0.2s ease;
            }

            .table tbody tr:hover {
                background-color: rgba(13, 110, 253, 0.05);
            }

            .table .badge {
                font-weight: 500;
            }

            .small-status {
                font-size: 0.65em;
            }

            @media (max-width: 767.98px) {
                .table-responsive {
                    margin: 0 -1rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearButton');
    const statusFilters = document.querySelectorAll('.status-filter');
    const issuesContainer = document.getElementById('issuesContainer');
    const emptyState = document.getElementById('emptyState');
    const issueCount = document.getElementById('issueCount');
    let searchTimeout;

    function getIssueRows() {
        return Array.from(issuesContainer.querySelectorAll('.issue-item'));
    }

    function filterIssues() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedStatuses = Array.from(statusFilters)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value.toLowerCase().trim());

        let visibleCount = 0;
        let anyVisible = false;

        getIssueRows().forEach(row => {
            const issueType = row.dataset.issueType;
            const location = row.dataset.location;
            const description = row.dataset.description;
            const status = row.dataset.status;
            const assignee = row.dataset.assignee;

            const matchesSearch = searchTerm === '' ||
                                issueType.includes(searchTerm) ||
                                location.includes(searchTerm) ||
                                description.includes(searchTerm) ||
                                assignee.includes(searchTerm);

            const matchesStatus = selectedStatuses.some(selectedStatus =>
                status === selectedStatus
            );

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
                anyVisible = true;
            } else {
                row.style.display = 'none';
            }
        });

        issueCount.textContent = `${visibleCount} issue${visibleCount !== 1 ? 's' : ''} found`;

        if (!anyVisible) {
            issuesContainer.classList.add('d-none');
            emptyState.classList.remove('d-none');
        } else {
            issuesContainer.classList.remove('d-none');
            emptyState.classList.add('d-none');
        }
    }

    // Search input handler with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterIssues, 300);
    });

    // Clear button handler
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        statusFilters.forEach(filter => {
            filter.checked = true;
        });
        filterIssues();
    });

    // Status filter handlers
    statusFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            const currentlyChecked = Array.from(statusFilters).filter(cb => cb.checked).length;

            if (currentlyChecked === 0) {
                this.checked = true;
            }

            filterIssues();
        });
    });

    // Initial filter
    filterIssues();
});
        </script>
    @endpush
@endsection
