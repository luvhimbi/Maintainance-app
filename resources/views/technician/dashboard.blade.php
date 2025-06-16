@extends('layouts.TechnicianNavbar')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid px-4 py-4"> {{-- Added py-4 for top/bottom spacing --}}

        <div class="row mb-4">
            <div class="col-12">
                <div class="welcome-card bg-white p-4 rounded-4 shadow-sm"> {{-- Added rounded-4 --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h1 class="h3 fw-bold mb-2 text-dark">Welcome back, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!</h1>
                            <p class="text-muted mb-0">Here's what's happening with your tasks today</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <span class="badge bg-light text-dark fs-6 py-2 px-3 rounded-pill"> {{-- Added padding and rounded-pill --}}
                                <i class="fas fa-calendar-day me-2"></i>
                                {{ now()->format('l, F j, Y') }}
                            </span>
                        </div>
                    </div>

                    @if($overdueCount > 0)
                        <div class="alert alert-danger mt-3 d-flex align-items-center rounded-3 shadow-sm" role="alert"> {{-- Added rounded-3 and shadow-sm --}}
                            <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                            <div>
                                <h5 class="alert-heading mb-1 fw-bold">You have {{ $overdueCount }} overdue task(s)!</h5>
                                <p class="mb-0">Please prioritize these tasks immediately.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden"> {{-- Added rounded-4 --}}
                    <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4"> {{-- Added px-4 and rounded-top-4 --}}
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <h5 class="card-title mb-0 fw-bold text-dark">
                                <i class="fas fa-clipboard-list me-2 text-primary"></i>Your Active Tasks
                            </h5>
                            <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                                <div class="input-group rounded-pill overflow-hidden shadow-sm-sm"> {{-- Added rounded-pill and shadow --}}
                                    <span class="input-group-text bg-light border-0 ps-3"> {{-- Removed border-end-0 --}}
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" id="taskSearch" class="form-control border-0 pe-3" placeholder="Search tasks..." aria-label="Search tasks"> {{-- Removed border-start-0 ps-0 --}}
                                    <button class="btn btn-outline-secondary border-0" type="button" id="clearSearch" aria-label="Clear search"> {{-- Removed border-start-0 --}}
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary rounded-pill px-3 py-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> {{-- Added rounded-pill and padding --}}
                                        <i class="fas fa-filter me-1"></i> Status
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-3"> {{-- Added shadow and rounded --}}
                                        @foreach(['Pending', 'In Progress'] as $status)
                                            <li>
                                                <div class="dropdown-item px-3 py-2"> {{-- Added padding --}}
                                                    <div class="form-check">
                                                        <input class="form-check-input status-filter" type="checkbox" value="{{ strtolower($status) }}" id="filter{{ str_replace(' ', '', $status) }}" checked> {{-- Lowercased value --}}
                                                        <label class="form-check-label" for="filter{{ str_replace(' ', '', $status) }}">{{ $status }}</label>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if($tasks->isEmpty())
                            <div class="text-center py-5 px-3"> {{-- Added px-3 --}}
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="fw-bold text-dark">No active tasks</h5>
                                <p class="text-muted">You have no pending or in-progress tasks currently assigned.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4">Task ID</th>
                                        <th scope="col">Issue Details</th>
                                        <th scope="col">Priority</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tasksTableBody">
                                    @foreach ($tasks as $task)
                                        @if($task->issue_status !== 'Completed' && $task->issue_status !== 'Closed') {{-- Added check for 'Closed' --}}
                                        <tr class="task-row border-bottom" {{-- Added border-bottom for row separation --}}
                                        data-id="{{ $task->task_id }}"
                                            data-title="{{ strtolower(trim($task->issue->issue_type)) }}"
                                            data-description="{{ strtolower(trim($task->issue->issue_description)) }}"
                                            data-status="{{ strtolower(trim($task->issue_status)) }}"
                                            data-priority="{{ strtolower(trim($task->priority)) }}"
                                            data-location="{{ strtolower(trim($task->issue->location->building_name ?? '') . ' ' . trim($task->issue->location->room_number ?? '')) }}"
                                            data-due="{{ $task->expected_completion->format('Y-m-d') }}"
                                            data-overdue="{{ $task->expected_completion->isPast() ? 'true' : 'false' }}">

                                            <td class="fw-bold ps-4">#{{ $task->task_id }}</td> {{-- Added ps-4 --}}

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="task-icon bg-primary-subtle rounded-circle p-2 me-3"> {{-- Used primary-subtle --}}
                                                            <i class="fas {{ $task->getIssueIcon() }} text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 text-dark fw-semibold">{{ $task->issue->issue_type }} Issue</h6> {{-- Adjusted text --}}
                                                        <small class="text-muted">{{ Str::limit($task->issue->issue_description, 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="badge rounded-pill priority-badge px-3 py-2 fw-medium
                                                    @if ($task->priority == 'Low') bg-success-subtle text-success
                                                    @elseif ($task->priority == 'Medium') bg-warning-subtle text-warning
                                                    @elseif ($task->priority == 'High') bg-danger-subtle text-danger
                                                    @endif">
                                                    <i class="fas fa-flag me-1"></i>{{ $task->priority }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge rounded-pill status-badge px-3 py-2 fw-medium
                                                    @if ($task->issue_status == 'Pending') bg-info-subtle text-info
                                                    @elseif ($task->issue_status == 'In Progress') bg-primary-subtle text-primary
                                                    @endif">
                                                    <i class="fas fa-circle me-1 small-status-dot"></i> {{ $task->issue_status }} {{-- Added dot icon --}}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-column">
                                                    <small class="text-muted">Due</small>
                                                    <span class="fw-bold @if($task->expected_completion->isPast()) text-danger @endif">
                                                        {{ $task->expected_completion->format('M d, Y') }}
                                                        @if($task->expected_completion->isPast())
                                                            <span class="badge bg-danger ms-2 rounded-pill">Overdue</span> {{-- Added rounded-pill --}}
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                    <div>
                                                        <a href="{{ route('technician.directions', [
                                                            'building' => $task->issue->building->building_name ?? 'N/A',
                                                            'room' => $task->issue->room->room_number ?? 'N/A',
                                                            'lat' => $task->issue->building->latitude ?? '',
                                                            'lng' => $task->issue->building->longitude ?? ''
                                                        ]) }}" class="location-link text-decoration-none">
                                                            <div class="text-dark d-flex align-items-center">
                                                                <span>{{ $task->issue->building->building_name ?? 'N/A' }}</span>
                                                                <i class="fas fa-external-link-alt ms-2 text-primary small"></i>
                                                            </div>
                                                            <small class="text-muted">Room {{ $task->issue->room->room_number ?? 'N/A' }}</small>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('technician.task_details', $task->task_id) }}"
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2" {{-- Added rounded-pill and padding --}}
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div id="noResultsError" class="text-center py-5 px-3 d-none"> {{-- Added px-3 --}}
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="fw-bold text-dark">No tasks found</h5>
                                <p class="text-muted">We couldn't find any tasks matching your search or filters.</p>
                                <button class="btn btn-outline-primary rounded-pill px-4 py-2" id="resetFilters"> {{-- Added rounded-pill and padding --}}
                                    <i class="fas fa-sync-alt me-2"></i>Reset Filters
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>


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

        /*!* Custom subtle badge colors *!*/
        /*.bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }*/
        /*.text-primary { color: #0d6efd !important; }*/

        /*.bg-success-subtle { background-color: rgba(40, 167, 69, 0.1) !important; }*/
        /*.text-success { color: #28a745 !important; }*/

        /*.bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }*/
        /*.text-warning { color: #ffc107 !important; }*/

        /*.bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }*/
        /*.text-danger { color: #dc3545 !important; }*/

        /*.bg-info-subtle { background-color: rgba(23, 162, 184, 0.1) !important; }*/
        /*.text-info { color: #17a2b8 !important; }*/



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

        /* Specific styles for table and task rows */
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

        .task-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .small-status-dot {
            font-size: 0.6em;
            vertical-align: middle;
        }

        /* Search input group styling */
        .input-group.rounded-pill {
            border: 1px solid #ced4da;
            /*border-radius: 2rem; !* More rounded *!*/
            overflow: hidden;
        }
        .input-group.rounded-pill .form-control,
        .input-group.rounded-pill .input-group-text,
        .input-group.rounded-pill .btn {
            border: none !important; /* Remove individual borders */
            background-color: white;
        }
        .input-group.rounded-pill .form-control:focus {
            box-shadow: none; /* No focus shadow inside input group */
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

        /* Search highlight */
        .search-highlight {
            background-color: #FFF9C4; /* Light yellow */
            padding: 0 2px;

        }

        /* No results error state */
        #noResultsError {
            background-color: #fdfdfd;

            border: 1px solid #e9ecef;
        }

        .location-link {
            display: block;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .location-link:hover {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .location-link:hover .text-dark {
            color: #0d6efd !important;
        }

        .location-link:hover .fa-external-link-alt {
            transform: translateX(2px);
        }

        .location-link .fa-external-link-alt {
            transition: transform 0.2s ease;
            opacity: 0.7;
        }

        .location-link:hover .fa-external-link-alt {
            opacity: 1;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Bootstrap tooltips
            const tooltipList = [...document.querySelectorAll('[data-bs-toggle="tooltip"]')]
                .map(el => new bootstrap.Tooltip(el));

            // DOM references
            const searchInput = document.getElementById('taskSearch');
            const clearSearchBtn = document.getElementById('clearSearch');
            const statusFilters = document.querySelectorAll('.status-filter');
            const taskRows = document.querySelectorAll('.task-row');
            const noResultsError = document.getElementById('noResultsError');
            const resetFiltersBtn = document.getElementById('resetFilters');
            const tableHead = document.querySelector('thead');
            const tasksTableBody = document.getElementById('tasksTableBody'); // Ensure this is correctly referenced

            // Debounce utility
            let debounceTimer;
            function debounce(callback, delay) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(callback, delay);
            }

            // Task filtering
            function filterTasks() {
                const searchTerm = searchInput.value.trim().toLowerCase();
                const selectedStatuses = Array.from(statusFilters)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value.toLowerCase());

                let visibleCount = 0;

                taskRows.forEach(row => {
                    // Remove previous highlights before filtering
                    removeHighlights(row);

                    const matchesSearch = matchesText(row, searchTerm);
                    const matchesStatus = selectedStatuses.includes(row.dataset.status.toLowerCase());

                    if ((searchTerm === '' || matchesSearch) && matchesStatus) {
                        row.style.display = '';
                        highlightSearch(row, searchTerm);
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // UI state toggling
                if (visibleCount === 0) {
                    if (noResultsError) noResultsError.classList.remove('d-none');
                    if (tableHead) tableHead.classList.add('d-none');
                    if (tasksTableBody) tasksTableBody.classList.add('d-none'); // Hide tbody if no results
                } else {
                    if (noResultsError) noResultsError.classList.add('d-none');
                    if (tableHead) tableHead.classList.remove('d-none');
                    if (tasksTableBody) tasksTableBody.classList.remove('d-none'); // Show tbody if results
                }
            }

            // Match task row with search term
            function matchesText(row, searchTerm) {
                const fields = [
                    row.dataset.title,
                    row.dataset.description,
                    row.dataset.priority,
                    row.dataset.location,
                    row.dataset.status,
                    row.dataset.id,
                    row.dataset.due
                ];

                return fields.some(field => field && field.toLowerCase().includes(searchTerm));
            }

            // Highlight matching parts of text
            function highlightSearch(row, searchTerm) {
                if (!searchTerm) return;

                const selectors = [
                    '.fw-bold', // For Task ID
                    'h6.mb-0', // For Issue Type
                    'small.text-muted', // For Issue Description and Room Number
                    '.priority-badge',
                    '.status-badge',
                    'td:nth-child(5) span', // For Due Date
                    'td:nth-child(6) > div > div' // For Building Name
                ];

                selectors.forEach(selector => {
                    row.querySelectorAll(selector).forEach(element => {
                        // Avoid highlighting inside input fields or elements that shouldn't be modified
                        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.classList.contains('badge')) {
                            return;
                        }
                        const originalText = element.textContent;
                        // Only apply highlight if the element's text actually contains the search term
                        if (originalText.toLowerCase().includes(searchTerm)) {
                            const regex = new RegExp(`(${searchTerm})`, 'gi');
                            element.innerHTML = originalText.replace(regex, `<span class="search-highlight">$1</span>`);
                        }
                    });
                });
            }


            // Remove all highlights from a row
            function removeHighlights(row) {
                row.querySelectorAll('.search-highlight').forEach(span => {
                    const parent = span.parentNode;
                    // Replace the span with its text content
                    parent.replaceChild(document.createTextNode(span.textContent), span);
                });
            }

            // Reset filters and show all tasks
            function resetFilters() {
                searchInput.value = '';
                statusFilters.forEach(cb => cb.checked = true); // Check all status filters
                filterTasks();
            }

            // Event listeners
            if (searchInput) {
                searchInput.addEventListener('input', () => debounce(filterTasks, 200));
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', resetFilters);
            }

            statusFilters.forEach(cb => {
                cb.addEventListener('change', filterTasks);
            });

            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', resetFilters);
            }

            // Initial filter call to set the correct state on page load
            filterTasks();
        });
    </script>

@endsection
