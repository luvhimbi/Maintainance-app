@extends('layouts.StudentNavbar')

@section('title', ' Dashboard')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
            <div class="mb-3 mb-md-0">
                <h1 class="fw-bold mb-2 h3">Hi , {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h1>
                <p class="lead text-muted mb-0">Track and manage your reported maintenance issues</p>
            </div>
            <a href="{{ route('Student.createissue') }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                <i class="fas fa-plus me-2"></i> Report An Issue
            </a>
        </div>

        <!-- Search Bar Section -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="input-group">
                    <input type="text"
                           id="searchInput"
                           class="form-control form-control-lg"
                           placeholder="Search issues by type, location, or description..."
                           aria-label="Search issues">
                    <button class="btn btn-primary" id="searchButton">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button class="btn btn-outline-secondary" id="clearButton">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
                <div class="mt-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input status-filter" type="checkbox" value="Open" id="openCheck" checked>
                        <label class="form-check-label" for="openCheck">Open</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input status-filter" type="checkbox" value="In Progress" id="progressCheck" checked>
                        <label class="form-check-label" for="progressCheck">In Progress</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Issues Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-bold">Your Active Reported Issues</h3>
                <span class="text-muted" id="issueCount">{{ $issues->count() }} issues found</span>
            </div>
            <div class="card-body p-0" id="issuesContainer">
                @foreach ($issues as $issue)
                    <div class="border-bottom p-4 issue-item hover-bg-light transition position-relative"
                         data-issue-type="{{ strtolower($issue->issue_type) }}"
                         data-location="{{ strtolower($issue->location->building_name ?? '') }} {{ strtolower($issue->location->room_number ?? '') }}"
                         data-description="{{ strtolower($issue->issue_description) }}"
                         data-status="{{ $issue->issue_status }}">
                        <div class="row align-items-center">
                            <!-- Left Column - Issue Identity -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
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
                                        @endphp
                                        <span class="badge bg-{{ $urgencyColor }} rounded-circle p-3 shadow-sm">
                                            <i class="fas {{ $icon }} fa-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-semibold">{{ $issue->issue_type }} Issue</h5>
                                        <div class="text-muted">
                                            <span class="d-inline-block me-3">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $issue->location->building_name ?? 'N/A'}}, Room {{ $issue->location->room_number ?? 'N/A'}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Middle Column - Priority -->
                            <div class="col-md-3">
                                <div>
                                    <span class="badge bg-{{ $urgencyColor }} text-white px-3 py-1 rounded-pill">
                                        {{ $issue->urgency_level }} Priority
                                    </span>
                                </div>
                            </div>

                            <!-- Right Column - Actions -->
                            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                                    @if($issue->issue_status === 'Open')
                                        <a href="{{ route('Student.editissue', $issue->issue_id) }}"
                                           class="btn btn-outline-secondary px-3 py-2 btn-action">
                                            <i class="fas fa-pencil-alt me-1"></i> Edit
                                        </a>
                                    @endif

                                    <a href="{{ route('Student.issue_details', $issue->issue_id) }}"
                                       class="btn btn-outline-primary px-4 py-2 btn-action">
                                        Details <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Status Indicator -->
                        <div class="position-absolute top-0 end-0 mt-3 me-3">
                            <span class="badge
                                @if($issue->issue_status == 'Open') bg-primary
                                @elseif($issue->issue_status == 'In Progress') bg-warning text-dark
                                @endif px-3 py-1 rounded-pill">
                                <i class="fas fa-circle me-1 small-status"></i>
                                {{ $issue->issue_status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Empty State (hidden by default) -->
        <div class="text-center py-5 d-none" id="emptyState">
            <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
            <h4 class="fw-semibold">No issues found</h4>
            <p class="text-muted">Try adjusting your search</p>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchInput');
                const searchButton = document.getElementById('searchButton');
                const clearButton = document.getElementById('clearButton');
                const statusFilters = document.querySelectorAll('.status-filter');
                const issueItems = document.querySelectorAll('.issue-item');
                const issuesContainer = document.getElementById('issuesContainer');
                const emptyState = document.getElementById('emptyState');
                const issueCount = document.getElementById('issueCount');

                function filterIssues() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const selectedStatuses = Array.from(statusFilters)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value);

                    let visibleCount = 0;

                    issueItems.forEach(item => {
                        const issueType = item.dataset.issueType;
                        const location = item.dataset.location;
                        const description = item.dataset.description;
                        const status = item.dataset.status;

                        const matchesSearch = searchTerm === '' ||
                            issueType.includes(searchTerm) ||
                            location.includes(searchTerm) ||
                            description.includes(searchTerm);

                        const matchesStatus = selectedStatuses.length === 0 ||
                            selectedStatuses.includes(status);

                        if (matchesSearch && matchesStatus) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Update count
                    issueCount.textContent = `${visibleCount} issues found`;

                    // Show/hide empty state
                    if (visibleCount === 0) {
                        issuesContainer.classList.add('d-none');
                        emptyState.classList.remove('d-none');
                    } else {
                        issuesContainer.classList.remove('d-none');
                        emptyState.classList.add('d-none');
                    }
                }

                // Event listeners
                searchInput.addEventListener('input', filterIssues);
                searchButton.addEventListener('click', filterIssues);
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    Array.from(statusFilters).forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    filterIssues();
                });

                statusFilters.forEach(filter => {
                    filter.addEventListener('change', filterIssues);
                });

                // Initial filter
                filterIssues();
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .hover-bg-light:hover {
                background-color: #f8f9fa;
                transform: translateY(-2px);
            }
            .transition {
                transition: all 0.2s ease;
            }
            .small-status {
                font-size: 0.6em;
                vertical-align: middle;
            }
            .btn-action {
                transition: all 0.2s ease;
            }
            .btn-action:hover {
                transform: translateY(-1px);
            }
            .issue-item {
                cursor: pointer;
                border-left: 3px solid transparent;
            }
            .issue-item:hover {
                border-left-color: var(--bs-primary);
            }
            #searchInput:focus {
                border-color: var(--bs-primary);
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }
        </style>
    @endpush
@endsection
