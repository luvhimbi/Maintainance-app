@extends('layouts.StudentNavbar')

@section('title', ' Dashboard')

@section('content')
    <div class="container py-4 bg-body-tertiary"> {{-- Added bg-body-tertiary for a subtle page background --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
            <div>
                <h1 class="fw-bold mb-1 h3">Hi, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h1>
                <p class="lead text-muted mb-0">Track and manage your reported maintenance issues</p>
            </div>
            {{-- <a href="{{ route('Student.createissue') }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                <i class="fas fa-plus me-2"></i> Report An Issue
            </a> --}}
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">
                <div class="row g-3 align-items-center"> {{-- Increased gap slightly with g-3 --}}
                    <div class="col-12 col-md-7">
                        <div class="input-group input-group-lg">
                            <input type="text"
                                   id="searchInput"
                                   class="form-control"
                                   placeholder="Search by type, location, or description..."
                                   aria-label="Search issues">
                            <button class="btn btn-primary" id="searchButton" title="Search">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" id="clearButton" title="Clear">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
                        <div class="form-check form-check-inline px-2">
                            <input class="form-check-input status-filter" type="checkbox" value="open" id="openCheck" checked> {{-- Changed value to lowercase --}}
                            <label class="form-check-label badge rounded-pill bg-primary-subtle text-primary fw-normal py-2 px-3" for="openCheck" style="cursor:pointer;">Open</label> {{-- Adjusted padding for filter labels --}}
                        </div>
                        <div class="form-check form-check-inline px-2">
                            <input class="form-check-input status-filter" type="checkbox" value="in progress" id="progressCheck" checked> {{-- Changed value to lowercase --}}
                            <label class="form-check-label badge rounded-pill bg-warning-subtle text-warning fw-normal py-2 px-3" for="progressCheck" style="cursor:pointer;">In Progress</label> {{-- Adjusted padding for filter labels --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-bold h5">Your Active Reported Issues</h3>
                <span class="text-muted small" id="issueCount">{{ $issues->total() }} issues found</span>
            </div>
            <div class="card-body p-0" id="issuesContainer">
                @forelse ($issues as $issue)
                @php
                    $icon = match($issue->issue_type) {
                        'Plumbing' => 'fa-faucet-drip',
                        'Electrical' => 'fa-bolt-lightning',
                        'Furniture' => 'fa-couch',
                        'HVAC' => 'fa-fan',
                        'Internet' => 'fa-network-wired',
                        'Cleaning' => 'fa-broom',
                        default => 'fa-circle-exclamation'
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
                <div class="issue-card d-flex flex-column flex-md-row align-items-stretch border-bottom p-3 p-md-4 issue-item position-relative"
                    data-issue-type="{{ strtolower(trim($issue->issue_type)) }}"
                    data-location="{{ strtolower(trim($issue->location->building_name ?? '') . ' ' . trim($issue->location->room_number ?? '')) }}"
                    data-description="{{ strtolower(trim($issue->issue_description)) }}"
                    data-status="{{ strtolower(trim($issue->issue_status)) }}">
                    <div class="issue-card-bar bg-{{ $urgencyColor }}"></div>
                    <div class="d-flex align-items-center flex-grow-1 me-md-3">
                        <span class="badge bg-{{ $urgencyColor }}-subtle text-{{ $urgencyColor }} rounded-circle p-3 shadow-sm me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas {{ $icon }} fa-lg"></i>
                        </span>
                        <div>
                            <h5 class="mb-1 fw-semibold">{{ $issue->issue_type }} Issue</h5>
                            <div class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1 text-secondary"></i>
                                {{ $issue->location->building_name ?? 'N/A'}}, Room {{ $issue->location->room_number ?? 'N/A'}}
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="fas fa-align-left me-1 text-secondary"></i>
                                {{ \Illuminate\Support\Str::limit($issue->issue_description, 50) }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-start align-items-md-end justify-content-center ms-md-auto mt-3 mt-md-0 text-md-end" style="min-width: 120px;"> {{-- Adjusted alignment and min-width --}}
                        <span class="badge bg-{{ $urgencyColor }}-subtle text-{{ $urgencyColor }} px-3 py-1 rounded-pill mb-2 fw-medium"> {{-- Subtle badge --}}
                            {{ $issue->urgency_level }} Priority
                        </span>
                        <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-1 rounded-pill fw-medium"> {{-- Subtle badge --}}
                            <i class="fas fa-circle me-1 small-status"></i>
                            {{ $issue->issue_status }}
                        </span>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-md-center ms-md-4 mt-3 mt-md-0">
                        @if($issue->issue_status === 'Open')
                            <a href="{{ route('Student.editissue', $issue->issue_id) }}"
                               class="btn btn-sm btn-outline-secondary btn-action px-3 py-2"> {{-- Standardized padding, btn-sm --}}
                                <i class="fas fa-pencil-alt me-1"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('Student.issue_details', $issue->issue_id) }}"
                           class="btn btn-sm btn-primary btn-action px-3 py-2"> {{-- Standardized padding, btn-sm, changed to primary fill --}}
                            Details <i class="fas fa-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
                @empty
                {{-- This part is primarily for non-JS scenarios or initial render. JS will show #emptyState --}}
                {{-- If JS is enabled, #emptyState below will be shown instead by the script. --}}
                @if($issues->isEmpty()) {{-- Explicit check for initial load --}}
                <div class="text-center py-5 px-3">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
                    <h4 class="fw-semibold mb-2">No issues reported yet.</h4>
                    <p class="text-muted mb-3">Ready to report your first maintenance issue?</p>
                    <a href="{{ route('Student.createissue') }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                        <i class="fas fa-plus me-2"></i> Report An Issue
                    </a>
                </div>
                @endif
                @endforelse
            </div>
            @if($issues->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $issues->withQueryString()->links('pagination::bootstrap-5') }}
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

    @push('scripts')
        <script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const clearButton = document.getElementById('clearButton');
    const statusFilters = document.querySelectorAll('.status-filter');
    const issuesContainer = document.getElementById('issuesContainer');
    const emptyState = document.getElementById('emptyState');
    const issueCount = document.getElementById('issueCount');

    // Always get the latest list of issue items (for pagination)
    function getIssueRows() {
        return Array.from(issuesContainer.querySelectorAll('.issue-item'));
    }

    function filterIssues() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedStatuses = Array.from(statusFilters)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value.trim());

        let visibleCount = 0;
        let anyVisible = false;

        getIssueRows().forEach(item => {
            // Use textContent for all text in the card for robust search
            const text = item.textContent.toLowerCase();
            const status = item.dataset.status.trim();

            const matchesSearch = searchTerm === '' || text.includes(searchTerm);
            const matchesStatus = selectedStatuses.length === 0 || selectedStatuses.includes(status);

            if (matchesSearch && matchesStatus) {
                item.style.display = '';
                visibleCount++;
                anyVisible = true;
            } else {
                item.style.display = 'none';
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

    // Search on input
    searchInput.addEventListener('input', filterIssues);

    // Search on button click
    searchButton.addEventListener('click', function(e) {
        e.preventDefault();
        filterIssues();
    });

    // Clear search and filters
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        document.getElementById('openCheck').checked = true;
        document.getElementById('progressCheck').checked = true;
        filterIssues();
    });

    // Status filter logic
    statusFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            if (this.checked) {
                statusFilters.forEach(otherFilter => {
                    if (otherFilter !== this) {
                        otherFilter.checked = false;
                    }
                });
            } else {
                const currentlyChecked = Array.from(statusFilters).filter(cb => cb.checked).length;
                if (currentlyChecked === 0) {
                    this.checked = true;
                }
            }
            filterIssues();
        });
    });

    // Initial filter
    filterIssues();
});
        </script>
    @endpush

    @push('styles')
        <style>
            body {
                /* Assuming StudentNavbar might not set this, or to override */
                background-color: #f8f9fa; /* Bootstrap's $gray-100 or bg-light equivalent */
                min-height: 100vh; /* Ensure body takes full viewport height */
                display: flex; /* Enable flexbox */
                flex-direction: column; /* Arrange content in a column */
            }
            .issue-card {
                background: #fff;
                border-radius: 0.75rem; /* 12px */
                margin-bottom: 1rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05); /* Softer shadow */
                position: relative;
                overflow: hidden;
                transition: box-shadow 0.25s ease-out, transform 0.25s ease-out;
                /* cursor: pointer; /* Add if the whole card is a link to details */
            }
            .issue-card:hover {
                box-shadow: 0 7px 22px rgba(13,110,253,0.12); /* More pronounced, themed shadow */
                transform: translateY(-4px); /* Slightly more lift, no scale to avoid blur */
            }
            .issue-card-bar {
                width: 6px;
                min-width: 6px; /* Ensure it's visible */
                height: 100%;
                border-radius: 6px 0 0 6px; /* Match card radius if card is rounded on left */
                position: absolute;
                left: 0;
                top: 0;
            }
            .btn-action {
                transition: all 0.2s ease-in-out;
            }
            .btn-action:hover, .btn-action:focus {
                transform: translateY(-2px) scale(1.03); /* Keep button micro-interaction */
                box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            }
            .small-status {
                font-size: 0.65em; /* Slightly smaller dot */
                vertical-align: middle;
            }
            #searchInput:focus {
                border-color: var(--bs-primary);
                box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15); /* Use BS variables */
            }
            .form-check-label.badge:hover {
                opacity: 0.85;
            }

            /* Responsive tweaks for issue card layout */
            @media (max-width: 767.98px) { /* Using Bootstrap's md breakpoint */
                .issue-card {
                    /* flex-direction: column !important; Is already default for the main div due to flex-column class */
                    /* align-items: flex-start !important; */
                    padding: 1rem !important; /* Consistent padding */
                }
                .issue-card-bar {
                    height: 6px;
                    width: 100%;
                    border-radius: 6px 6px 0 0; /* Top bar on mobile */
                    left: 0;
                    top: 0;
                }
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
        </style>
    @endpush
@endsection
