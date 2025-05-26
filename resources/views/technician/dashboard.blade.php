@extends('layouts.TechnicianNavbar')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid px-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('technician.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Overview</li>
            </ol>
        </nav>
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
                                <!-- Search Bar -->
                                <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                                    <input type="text" id="taskSearch" class="form-control" placeholder="Search tasks..." aria-label="Search tasks">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch" aria-label="Clear search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Filter Dropdown -->
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter me-1"></i> Status
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @foreach(['Pending', 'In Progress'] as $status)
                                            <li>
                                                <div class="dropdown-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input status-filter" type="checkbox" value="{{ $status }}" id="filter{{ str_replace(' ', '', $status) }}" checked>
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
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="fw-bold">No active tasks</h5>
                                <p class="text-muted">You have no pending or in-progress tasks currently assigned.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col">Task ID</th>
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
                                        @if($task->issue_status !== 'Completed')
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

                                                <!-- Issue Details -->
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

                                                <!-- Priority -->
                                                <td>
                                                <span class="badge priority-badge
                                                    @if ($task->priority == 'Low') bg-success
                                                    @elseif ($task->priority == 'Medium') bg-warning
                                                    @elseif ($task->priority == 'High') bg-danger
                                                    @endif">
                                                    <i class="fas fa-flag me-1"></i>{{ $task->priority }}
                                                </span>
                                                </td>

                                                <!-- Status -->
                                                <td>
                                                <span class="badge status-badge
                                                    @if ($task->issue_status == 'Pending') bg-secondary
                                                    @elseif ($task->issue_status == 'In Progress') bg-primary
                                                    @endif">
                                                    {{ $task->issue_status }}
                                                </span>
                                                </td>

                                                <!-- Due Date -->
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

                                                <!-- Location -->
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                        <div>
                                                            <div>{{ $task->issue->location->building_name ?? 'N/A' }}</div>
                                                            <small class="text-muted">Room {{ $task->issue->location->room_number ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Actions -->
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

                            <!-- No Results -->
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
            const emptyState = document.querySelector('.text-center.py-5');

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
                    const matchesSearch = matchesText(row, searchTerm);
                    const matchesStatus = selectedStatuses.includes(row.dataset.status.toLowerCase());

                    if ((searchTerm === '' || matchesSearch) && matchesStatus) {
                        row.style.display = '';
                        highlightSearch(row, searchTerm);
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                        removeHighlights(row);
                    }
                });

                // UI state toggling
                if (visibleCount === 0) {
                    if (noResultsError) noResultsError.classList.remove('d-none');
                    if (tableHead) tableHead.classList.add('d-none');
                } else {
                    if (noResultsError) noResultsError.classList.add('d-none');
                    if (tableHead) tableHead.classList.remove('d-none');
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
                    'h6.mb-0',
                    'small.text-muted',
                    '.priority-badge',
                    '.status-badge',
                    'td:nth-child(5) .fw-bold',
                    'td:nth-child(6) > div > div'
                ];

                selectors.forEach(selector => {
                    const element = row.querySelector(selector);
                    if (element) {
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        const originalText = element.textContent;
                        element.innerHTML = originalText.replace(regex, `<span class="search-highlight">$1</span>`);
                    }
                });
            }

            // Remove all highlights from a row
            function removeHighlights(row) {
                row.querySelectorAll('.search-highlight').forEach(span => {
                    const parent = span.parentNode;
                    parent.innerHTML = parent.textContent;
                });
            }

            // Reset filters and show all tasks
            function resetFilters() {
                searchInput.value = '';
                statusFilters.forEach(cb => cb.checked = true);
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


            // Initial filter call
            filterTasks();
        });
    </script>

@endsection
