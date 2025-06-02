@extends('layouts.AdminNavBar')

@section('title', 'View All Tasks')

@section('content')
    <div class="container-fluid py-4"> {{-- Changed to container-fluid for full width, added py-4 --}}
        <div class="card border-0 shadow-sm rounded-4"> {{-- Added rounded-4 for consistent styling --}}
            <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4"> {{-- Added px-4 and rounded-top-4 --}}
                <div class="row align-items-center g-3"> {{-- Using Bootstrap row for better column control --}}
                    <div class="col-12 col-md-6"> {{-- Column for title --}}
                        <h2 class="h5 mb-1 fw-bold text-dark">Task Management</h2>
                        <p class="text-muted small mb-0">Manage and monitor all technician tasks</p>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 offset-lg-2 d-flex flex-column flex-sm-row align-items-center justify-content-end gap-3"> {{-- Added flexbox for alignment --}}
                        <div class="input-group rounded-pill overflow-hidden shadow-sm-sm flex-grow-1"> {{-- Styled search input --}}
                            <span class="input-group-text bg-light border-0 ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                            <input type="text" id="taskSearch" class="form-control border-0 pe-3" placeholder="Search tasks by ID, issue, technician, status..." aria-label="Search tasks"> {{-- Expanded placeholder --}}
                            <button class="btn btn-outline-secondary border-0" type="button" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="overdue-alert badge bg-danger-subtle text-danger py-2 px-3 rounded-pill fw-medium"> {{-- Styled overdue badge --}}
                            <i class="fas fa-exclamation-circle me-1"></i>
                            Overdue Tasks: {{ $overdueCount }}
                        </div>
                    </div>
                </div>
                <div id="searchFeedback" class="mt-2 small text-muted text-end" style="display: none;"> {{-- Moved feedback below input group --}}
                    <span id="resultCount">0</span> tasks found
                </div>
                {{-- Removed searchError div --}}
            </div>

            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0 rounded-3 shadow-sm" role="alert"> {{-- Added rounded-3 and shadow-sm --}}
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div id="tasksTableContainer" class="{{ $tasks->isEmpty() ? 'd-none' : '' }}"> {{-- Container for table, hidden if no tasks initially --}}
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover"> {{-- Added table-hover for better UX --}}
                            <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th class="py-3">Issue Details</th>
                                <th class="py-3">Technician Name</th>
                                <th class="py-3">Assigned On</th>
                                <th class="py-3">Due Date</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Priority</th>
                                <th class="pe-4 text-end py-3">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tasksTableBody">
                            @forelse($tasks as $task)
                                <tr class="border-top task-row
                                    @if($task->issue_status == 'Completed') table-success-light
                                    @elseif($task->expected_completion->isPast() && $task->issue_status != 'Completed') table-danger-light @endif"
                                    data-taskid="{{ $task->task_id }}"
                                    data-issueid="{{ $task->issue_id }}"
                                    data-issuetitle="{{ strtolower($task->issue->title ?? '') }}"
                                    data-techname="{{ strtolower($task->assignee->first_name ?? '') . ' ' . strtolower($task->assignee->last_name ?? '') }}"
                                    data-assignmentdate="{{ $task->assignment_date->format('d M Y') }}"
                                    data-duedate="{{ $task->expected_completion->format('d M Y') }}"
                                    data-status="{{ strtolower($task->issue_status) }}"
                                    data-priority="{{ strtolower($task->priority) }}">
                                    <td class="ps-4 fw-semibold text-dark task-id">#{{ $task->task_id }}</td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-container bg-info-subtle text-info me-2 p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"> {{-- Styled icon --}}
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark issue-id">ISSUE-{{ $task->issue_id }}</div>
                                                <small class="text-muted issue-title">
                                                    @if($task->issue)
                                                        {{ Str::limit($task->issue->title, 25) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="technician-name">
                                        @if($task->assignee)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <span class="fw-bold small">{{ substr($task->assignee->first_name, 0, 1) }}{{ substr($task->assignee->last_name, 0, 1) }}</span>
                                                </div>
                                                <span class="text-dark fw-medium">{{ $task->assignee->first_name }} {{ $task->assignee->last_name }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary py-1 px-2 rounded-pill fw-medium">
                                                <i class="fas fa-user-times me-1"></i> Unassigned
                                            </span>
                                        @endif
                                    </td>

                                    <td class="assignment-date">
                                        <div class="text-muted small">
                                            {{ $task->assignment_date->format('d M Y') }}
                                        </div>
                                    </td>

                                    <td class="due-date">
                                        <div class="d-flex align-items-center">
                                            <div class="@if($task->expected_completion->isPast() && $task->issue_status != 'Completed') text-danger fw-medium @else text-dark fw-medium @endif">
                                                {{ $task->expected_completion->format('d M Y') }}
                                            </div>
                                            @if($task->expected_completion->isPast() && $task->issue_status != 'Completed')
                                                <span class="badge bg-danger ms-2 py-1 px-2 rounded-pill fw-medium">
                                                    <i class="fas fa-clock me-1"></i>Overdue
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="task-status">
                                        @php
                                            $statusClasses = [
                                                'Completed' => 'success',
                                                'In Progress' => 'primary',
                                                'Pending' => 'warning',
                                                'Open' => 'info',
                                                'Closed' => 'secondary'
                                            ];
                                            $statusIcon = [
                                                'Completed' => 'fa-check-circle',
                                                'In Progress' => 'fa-spinner fa-spin',
                                                'Pending' => 'fa-hourglass-half',
                                                'Open' => 'fa-folder-open',
                                                'Closed' => 'fa-times-circle'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusClasses[$task->issue_status] ?? 'secondary' }}-subtle text-{{ $statusClasses[$task->issue_status] ?? 'secondary' }} py-2 px-3 rounded-pill fw-medium">
                                            <i class="fas {{ $statusIcon[$task->issue_status] ?? 'fa-info-circle' }} me-1"></i>
                                            {{ $task->issue_status }}
                                        </span>
                                    </td>

                                    <td class="task-priority">
                                        @php
                                            $priorityClasses = [
                                                'High' => 'danger',
                                                'Medium' => 'warning',
                                                'Low' => 'success'
                                            ];
                                            $priorityIcon = 'fa-flag';
                                        @endphp
                                        <span class="badge bg-{{ $priorityClasses[$task->priority] ?? 'secondary' }}-subtle text-{{ $priorityClasses[$task->priority] ?? 'secondary' }} py-2 px-3 rounded-pill fw-medium">
                                            <i class="fas {{ $priorityIcon }} me-1"></i>
                                            {{ $task->priority }}
                                        </span>
                                    </td>

                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('tasks.progress.show', $task->task_id) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2" {{-- Styled button --}}
                                               data-bs-toggle="tooltip"
                                               title="View Progress">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- This empty state will be handled by the JS below --}}
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- New Empty State for no tasks or no results --}}
                <div id="noTasksFoundState" class="text-center py-5 px-3 {{ $tasks->isNotEmpty() ? 'd-none' : '' }}"> {{-- Initially hidden if tasks exist --}}
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-tasks fa-4x text-muted"></i> {{-- Updated icon --}}
                    </div>
                    <h4 class="fw-bold mb-2 text-dark" id="emptyStateHeading">No Tasks Found</h4>
                    <p class="text-muted mb-4" id="emptyStateText">There are no maintenance tasks to display.</p>
                    <button class="btn btn-outline-secondary rounded-pill px-4 py-2 mt-2 d-none" id="resetEmptyStateFilters">
                        <i class="fas fa-sync-alt me-1"></i> Reset Filters
                    </button>
                </div>

                {{-- Conditional pagination links --}}
                @if ($tasks instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $tasks->hasPages())
                    <div class="card-footer bg-transparent border-0 pt-3 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} entries
                            </div>
                            <div>
                                {{ $tasks->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @endif
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

            /* Custom subtle badge colors */
            .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
            .text-primary { color: #0d6efd !important; }

            .bg-success-subtle { background-color: rgba(40, 167, 69, 0.1) !important; }
            .text-success { color: #28a745 !important; }

            .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
            .text-warning { color: #ffc107 !important; }

            .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
            .text-danger { color: #dc3545 !important; }

            .bg-info-subtle { background-color: rgba(23, 162, 184, 0.1) !important; }
            .text-info { color: #17a2b8 !important; }

            .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
            .text-secondary { color: #6c757d !important; }

            /* Buttons */
            .btn-primary {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
            .btn-primary:hover {
                background-color: #0b5ed7;
                border-color: #0a58ca;
            }

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

            /* Search input group styling */
            .input-group.rounded-pill {
                border: 1px solid #ced4da;
                border-radius: 2rem; /* More rounded */
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

            /* Table specific styles */
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

            /* Overdue and Completed row styling */
            .table-danger-light {
                background-color: rgba(220, 53, 69, 0.03) !important; /* Very light red */
                border-left: 4px solid #dc3545; /* Red left border */
            }
            .table-success-light {
                background-color: rgba(40, 167, 69, 0.03) !important; /* Very light green */
                border-left: 4px solid #28a745; /* Green left border */
            }

            /* Search highlight */
            .search-highlight {
                background-color: #FFF9C4; /* Light yellow */
                padding: 0 2px;
                border-radius: 3px;
            }

            /* Empty state styling */
            .empty-state-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(108, 117, 125, 0.1); /* Secondary subtle for empty */
                border-radius: 50%;
            }
            .empty-state-icon i {
                color: #6c757d; /* Muted color for icon */
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });

                // Task search functionality
                const searchInput = document.getElementById('taskSearch');
                const clearButton = document.getElementById('clearSearch');
                const tasksTableBody = document.getElementById('tasksTableBody');
                const tasksTableContainer = document.getElementById('tasksTableContainer');
                const noTasksFoundState = document.getElementById('noTasksFoundState');
                const emptyStateHeading = document.getElementById('emptyStateHeading');
                const emptyStateText = document.getElementById('emptyStateText');
                const resetEmptyStateFiltersButton = document.getElementById('resetEmptyStateFilters');
                const searchFeedback = document.getElementById('searchFeedback');
                const resultCount = document.getElementById('resultCount');

                // Function to highlight text matches
                function highlightText(element, searchTerm) {
                    if (!element || !searchTerm) return;

                    const text = element.textContent;
                    const lowerText = text.toLowerCase();
                    let startIndex = 0;

                    // If the search term isn't found in this element, return
                    if (!lowerText.includes(searchTerm.toLowerCase())) return;

                    // Clear the element's content while preserving its original text for re-highlighting
                    const originalContent = element.innerHTML;
                    element.innerHTML = ''; // Clear current HTML

                    // Find all occurrences and highlight them
                    while (startIndex < text.length) {
                        const matchIndex = lowerText.indexOf(searchTerm.toLowerCase(), startIndex);

                        if (matchIndex === -1) {
                            // No more matches, add the remaining text
                            element.appendChild(document.createTextNode(text.substring(startIndex)));
                            break;
                        }

                        // Add text before the match
                        if (matchIndex > startIndex) {
                            element.appendChild(document.createTextNode(text.substring(startIndex, matchIndex)));
                        }

                        // Add the highlighted match
                        const highlightSpan = document.createElement('span');
                        highlightSpan.className = 'search-highlight';
                        highlightSpan.textContent = text.substring(matchIndex, matchIndex + searchTerm.length);
                        element.appendChild(highlightSpan);

                        // Move past this match
                        startIndex = matchIndex + searchTerm.length;
                    }
                }

                // Function to remove all highlights from a row
                function removeHighlightsFromRow(row) {
                    row.querySelectorAll('.search-highlight').forEach(span => {
                        const parent = span.parentNode;
                        parent.replaceChild(document.createTextNode(span.textContent), span);
                        parent.normalize(); // Merge adjacent text nodes
                    });
                }

                // Function to filter table rows based on search input
                function filterTasks() {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    let visibleCount = 0;

                    // Get all actual task rows
                    const taskRows = Array.from(tasksTableBody.children).filter(row => row.classList.contains('task-row'));

                    taskRows.forEach(row => {
                        // Remove any existing highlights first
                        removeHighlightsFromRow(row);

                        const taskId = row.dataset.taskid;
                        const issueId = row.dataset.issueid;
                        const issueTitle = row.dataset.issuetitle;
                        const techName = row.dataset.techname;
                        const assignmentDate = row.dataset.assignmentdate;
                        const dueDate = row.dataset.duedate;
                        const status = row.dataset.status;
                        const priority = row.dataset.priority;

                        const matchesSearch =
                            taskId.includes(searchTerm) ||
                            issueId.includes(searchTerm) ||
                            issueTitle.includes(searchTerm) ||
                            techName.includes(searchTerm) ||
                            assignmentDate.includes(searchTerm) ||
                            dueDate.includes(searchTerm) ||
                            status.includes(searchTerm) ||
                            priority.includes(searchTerm);

                        if (matchesSearch) {
                            row.style.display = '';
                            if (searchTerm) { // Only highlight if there's a search term
                                highlightText(row.querySelector('.task-id'), searchTerm);
                                highlightText(row.querySelector('.issue-id'), searchTerm);
                                highlightText(row.querySelector('.issue-title'), searchTerm);
                                highlightText(row.querySelector('.technician-name'), searchTerm);
                                highlightText(row.querySelector('.assignment-date'), searchTerm);
                                highlightText(row.querySelector('.due-date'), searchTerm);
                                highlightText(row.querySelector('.task-status'), searchTerm);
                                highlightText(row.querySelector('.task-priority'), searchTerm);
                            }
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Update UI based on visible count
                    if (visibleCount === 0) {
                        tasksTableContainer.classList.add('d-none');
                        noTasksFoundState.classList.remove('d-none');

                        // Adjust empty state message based on whether a search term is present
                        if (searchTerm) {
                            emptyStateHeading.textContent = 'No Matching Tasks Found';
                            emptyStateText.textContent = 'We couldn\'t find any tasks matching your search criteria.';
                            resetEmptyStateFiltersButton.classList.remove('d-none'); // Show reset filters button
                            searchFeedback.style.display = 'none';
                        } else {
                            emptyStateHeading.textContent = 'No Tasks Found';
                            emptyStateText.textContent = 'There are no maintenance tasks to display.';
                            resetEmptyStateFiltersButton.classList.add('d-none'); // Hide reset filters button
                            searchFeedback.style.display = 'none';
                        }
                    } else {
                        tasksTableContainer.classList.remove('d-none');
                        noTasksFoundState.classList.add('d-none');
                        searchFeedback.style.display = 'block';
                        resultCount.textContent = visibleCount;
                    }
                }

                // Add event listeners
                if (searchInput) {
                    searchInput.addEventListener('input', filterTasks);

                    clearButton.addEventListener('click', function() {
                        searchInput.value = '';
                        filterTasks();
                    });

                    resetEmptyStateFiltersButton?.addEventListener('click', function() {
                        searchInput.value = '';
                        filterTasks();
                        searchInput.focus();
                    });

                    // Add keyboard shortcut (Ctrl+F or Cmd+F) to focus search
                    document.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                            e.preventDefault();
                            searchInput.focus();
                        }
                    });
                }

                // Initial call to filterTasks to set the correct state on page load
                filterTasks();
            });
        </script>
@endsection
