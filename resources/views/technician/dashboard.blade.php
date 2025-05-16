@extends('layouts.TechnicianNavbar')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid px-4">
        <!-- Welcome & Stats Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="welcome-card bg-white p-4 rounded-3 shadow-sm">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h1 class="h3 fw-bold mb-2">Welcome back, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!</h1>
                            <p class="text-muted mb-0">Here's what's happening with your tasks today</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <span class="badge bg-light text-dark fs-6">
                                <i class="fas fa-calendar-day me-2"></i>
                                {{ now()->format('l, F j, Y') }}
                            </span>
                        </div>
                    </div>

                    @if($overdueCount > 0)
                        <div class="alert alert-danger mt-3 d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                            <div>
                                <h5 class="alert-heading mb-1">You have {{ $overdueCount }} overdue task(s)!</h5>
                                <p class="mb-0">Please prioritize these tasks immediately.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Task Overview Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="fas fa-clipboard-list me-2 text-primary"></i>Your Active Tasks
                            </h5>
                            <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" id="taskSearch" class="form-control" placeholder="Search tasks...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-filter me-1"></i> Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <div class="dropdown-item">
                                                <div class="form-check">
                                                    <input class="form-check-input status-filter" type="checkbox" value="Pending" id="filterPending" checked>
                                                    <label class="form-check-label" for="filterPending">Pending</label>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-item">
                                                <div class="form-check">
                                                    <input class="form-check-input status-filter" type="checkbox" value="In Progress" id="filterProgress" checked>
                                                    <label class="form-check-label" for="filterProgress">In Progress</label>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($tasks->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="fw-bold">No active tasks</h5>
                                <p class="text-muted">You have no pending or in-progress tasks currently assigned.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Task ID</th>
                                        <th>Issue Details</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Location</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tasksTableBody">
                                    @foreach ($tasks as $task)
                                        @if($task->issue_status != 'Completed')
                                            <tr class="task-row"
                                                data-id="{{ $task->task_id }}"
                                                data-title="{{ $task->issue->issue_type }}"
                                                data-description="{{ $task->issue->issue_description }}"
                                                data-status="{{ $task->issue_status }}"
                                                data-priority="{{ $task->priority }}"
                                                data-location="{{ $task->issue->location->building_name ?? '' }} {{ $task->issue->location->room_number ?? '' }}"
                                                data-due="{{ $task->expected_completion->format('Y-m-d') }}"
                                                data-overdue="{{ $task->expected_completion->isPast() ? 'true' : 'false' }}">
                                                <td class="fw-bold">#{{ $task->task_id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="task-icon bg-light-primary rounded-circle p-2 me-3">
                                                                <i class="fas {{ $task->getIssueIcon() }} text-primary"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $task->issue->issue_type }} Issue Type</h6>
                                                            <small class="text-muted">{{ Str::limit($task->issue->issue_description, 50) }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge priority-badge
                                                        @if ($task->priority == 'Low') bg-success
                                                        @elseif ($task->priority == 'Medium') bg-warning
                                                        @elseif ($task->priority == 'High') bg-danger
                                                        @endif">
                                                        <i class="fas fa-flag me-1"></i>{{ $task->priority }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge status-badge
                                                        @if ($task->issue_status == 'Pending') bg-secondary
                                                        @elseif ($task->issue_status == 'In Progress') bg-primary
                                                        @endif">
                                                        {{ $task->issue_status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small class="text-muted">Due</small>
                                                        <span class="fw-bold @if($task->expected_completion->isPast()) text-danger @endif">
                                                            {{ $task->expected_completion->format('M d, Y') }}
                                                            @if($task->expected_completion->isPast())
                                                                <span class="badge bg-danger ms-2">Overdue</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                        <div>
                                                            <div>{{ $task->issue->location->building_name ?? 'N/A' }}</div>
                                                            <small class="text-muted">Room {{ $task->issue->location->room_number ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('technician.task_details', $task->task_id) }}"
                                                           class="btn btn-sm btn-outline-primary"
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

                            <!-- No Results Error State (initially hidden) -->
                            <div id="noResultsError" class="text-center py-5 d-none">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="fw-bold">No tasks found</h5>
                                <p class="text-muted">We couldn't find any tasks matching your search or filters.</p>
                                <button class="btn btn-outline-primary" id="resetFilters">
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
        /* Enhanced Styles */
        .task-card {
            border-left: 4px solid;
            transition: all 0.2s ease;
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .task-card.high-priority {
            border-left-color: #dc3545;
        }
        .task-card.medium-priority {
            border-left-color: #ffc107;
        }
        .task-card.low-priority {
            border-left-color: #28a745;
        }
        .task-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-highlight {
            background-color: #FFF9C4;
            padding: 0 2px;
            border-radius: 3px;
        }
        .status-badge {
            min-width: 100px;
            text-align: center;
        }
        .priority-badge {
            min-width: 80px;
            text-align: center;
        }
        #noResultsError {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(tooltipTriggerEl => {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Search and Filter Elements
            const searchInput = document.getElementById('taskSearch');
            const clearSearch = document.getElementById('clearSearch');
            const statusFilters = document.querySelectorAll('.status-filter');
            const taskRows = document.querySelectorAll('.task-row');
            const noResultsError = document.getElementById('noResultsError');
            const resetFiltersBtn = document.getElementById('resetFilters');
            const emptyState = document.querySelector('.text-center.py-5');

            // Filter tasks based on search and status
            function filterTasks() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedStatuses = Array.from(statusFilters)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value.toLowerCase());

                let visibleCount = 0;

                taskRows.forEach(row => {
                    const title = row.dataset.title.toLowerCase();
                    const description = row.dataset.description.toLowerCase();
                    const location = row.dataset.location.toLowerCase();
                    const status = row.dataset.status.toLowerCase();
                    const priority = row.dataset.priority.toLowerCase();
                    const taskId = row.querySelector('td:first-child').textContent.toLowerCase();

                    // Check if matches search term
                    const matchesSearch = searchTerm === '' ||
                        title.includes(searchTerm) ||
                        description.includes(searchTerm) ||
                        location.includes(searchTerm) ||
                        priority.includes(searchTerm) ||
                        taskId.includes(searchTerm);

                    // Check if matches status filter
                    const matchesStatus = selectedStatuses.length === 0 ||
                        selectedStatuses.includes(status);

                    // Show/hide row
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        highlightMatches(row, searchTerm);
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                        removeHighlights(row);
                    }
                });

                // Show appropriate state
                if (visibleCount === 0) {
                    // Hide the empty state (no tasks at all)
                    if (emptyState) emptyState.style.display = 'none';

                    // Show no results error (for search/filter)
                    if (noResultsError) noResultsError.classList.remove('d-none');

                    // Hide the table header if no results
                    document.querySelector('thead')?.classList.add('d-none');
                } else {
                    // Hide no results error
                    if (noResultsError) noResultsError.classList.add('d-none');

                    // Show empty state if no tasks exist at all
                    if (emptyState) emptyState.style.display = visibleCount === 0 ? '' : 'none';

                    // Show the table header
                    document.querySelector('thead')?.classList.remove('d-none');
                }
            }

            // Highlight matching text
            function highlightMatches(row, searchTerm) {
                if (!searchTerm) return;

                const elementsToHighlight = [
                    row.querySelector('h6.mb-0'), // Title
                    row.querySelector('small.text-muted'), // Description
                    row.querySelector('.priority-badge'), // Priority
                    row.querySelector('.status-badge'), // Status
                    row.querySelector('td:nth-child(5) .fw-bold'), // Due date
                    row.querySelector('td:nth-child(6) > div > div') // Location
                ];

                elementsToHighlight.forEach(el => {
                    if (!el) return;
                    const text = el.textContent;
                    const regex = new RegExp(searchTerm, 'gi');
                    el.innerHTML = text.replace(regex, match =>
                        `<span class="search-highlight">${match}</span>`
                    );
                });
            }

            // Remove highlights
            function removeHighlights(row) {
                const highlights = row.querySelectorAll('.search-highlight');
                highlights.forEach(highlight => {
                    const parent = highlight.parentNode;
                    parent.textContent = parent.textContent;
                });
            }

            // Reset all filters
            function resetFilters() {
                searchInput.value = '';
                statusFilters.forEach(filter => {
                    filter.checked = true;
                });
                filterTasks();
            }

            // Event Listeners
            if (searchInput) {
                searchInput.addEventListener('input', filterTasks);
            }

            if (clearSearch) {
                clearSearch.addEventListener('click', resetFilters);
            }

            statusFilters.forEach(filter => {
                filter.addEventListener('change', filterTasks);
            });

            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', resetFilters);
            }

            // Start Task button handler
            document.querySelectorAll('.start-task').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.dataset.taskId;
                    fetch(`/technician/tasks/${taskId}/start`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                });
            });

            // Initial filter
            filterTasks();
        });
    </script>
@endsection
