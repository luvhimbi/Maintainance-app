@extends('Layouts.AdminNavBar')
@section('title','View All Tasks')
@section('content')
<div class="container task-management-container py-4">
    <!-- Header Section -->
    <div class="header-section mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h2 class="page-title mb-1">Task Management</h2>
                <p class="page-subtitle text-muted">Manage and monitor all technician tasks</p>
            </div>
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
                <div id="searchFeedback" class="mt-2 small text-muted" style="display: none;">
                    <span id="resultCount">0</span> tasks found
                </div>
                <div id="searchError" class="mt-2 small text-danger" style="display: none;">
                    <i class="fas fa-exclamation-circle me-1"></i> No tasks match your search criteria
                </div>
                <div class="overdue-alert badge bg-danger bg-soft-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Overdue Tasks: {{ $overdueCount }}
                </div>
            </div>
        </div>
    </div>

    <!-- Task Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="table-light">
                            <th class="ps-4">ID</th>
                            <th>Issue Details</th>
                            <th>Technician Name</th>
                            <th>Assigned On</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr class="@if($task->issue_status == 'Completed') table-success-light
                                      @elseif($task->expected_completion->isPast() && $task->issue_status != 'Completed') table-danger-light @endif">
                                <!-- Task ID -->
                                <td class="ps-4 fw-semibold text-muted">#{{ $task->task_id }}</td>

                                <!-- Issue Details -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container text-danger me-2">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">ISSUE-{{ $task->issue_id }}</div>
                                            <small class="text-muted">
                                                @if($task->issue)
                                                    {{ Str::limit($task->issue->title, 25) }}
                                                @else
                                                    N/A
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <!-- Technician -->
                                <td>
                                    @if($task->assignee)
                                        <div class="d-flex align-items-center">

                                            <span>{{ $task->assignee->first_name }} {{ $task->assignee->last_name }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-secondary">
                                            <i class="fas fa-user-times me-1"></i> Unassigned
                                        </span>
                                    @endif
                                </td>

                                <!-- Assignment Date -->
                                <td>
                                    <div class="text-muted small">
                                        {{ $task->assignment_date->format('d M Y') }}
                                    </div>
                                </td>

                                <!-- Due Date with Overdue Warning -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="@if($task->expected_completion->isPast() && $task->issue_status != 'Completed') text-danger @else text-muted @endif">
                                            {{ $task->expected_completion->format('d M Y') }}
                                        </div>
                                        @if($task->expected_completion->isPast() && $task->issue_status != 'Completed')
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-clock me-1"></i>Overdue
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    @php
                                        $statusClasses = [
                                            'Completed' => 'success',
                                            'In Progress' => 'primary',
                                            'Pending' => 'warning'
                                        ];
                                    @endphp
                                    <span class="badge bg-soft-{{ $statusClasses[$task->issue_status] ?? 'secondary' }} text-{{ $statusClasses[$task->issue_status] ?? 'secondary' }}">
                                        {{ $task->issue_status }}
                                        @if($task->expected_completion->isPast() && $task->issue_status != 'Completed')
                                            <i class="fas fa-exclamation-triangle ms-1"></i>
                                        @endif
                                    </span>
                                </td>

                                <!-- Priority -->
                                <td>
                                    @php
                                        $priorityClasses = [
                                            'High' => 'danger',
                                            'Medium' => 'warning',
                                            'Low' => 'success'
                                        ];
                                    @endphp
                                    <span class="badge bg-soft-{{ $priorityClasses[$task->priority] ?? 'secondary' }} text-{{ $priorityClasses[$task->priority] ?? 'secondary' }}">
                                        <i class="fas fa-flag me-1"></i>
                                        {{ $task->priority }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="pe-4 text-end">
                                    <div class="action-buttons">
                                        <a href="{{ route('tasks.progress.show', $task->task_id) }}"
                                            class="btn btn-sm btn-soft-primary"
                                            data-bs-toggle="tooltip"
                                            title="View Progress">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="noResultsMessage" class="p-4 text-center" style="display: none;">
                    <div class="alert alert-warning d-inline-block w-100 shadow-sm" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-search-minus me-2"></i>No tasks found</h5>
                        <p class="mb-0">We couldn't find any tasks matching your search. Please try a different keyword.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Add these new styles */
    .table-danger-light {
        background-color: rgba(220, 53, 69, 0.03) !important;
        border-left: 3px solid #dc3545;
    }

    .overdue-alert {
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .table-danger-light td {
        position: relative;
    }

    .table-danger-light td:first-child::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background-color: #dc3545;
    }

    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }

    /* Search highlight style */
    .search-highlight {
        background-color: #ffeb3b;
        padding: 0 2px;
        border-radius: 2px;
        font-weight: bold;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Flash overdue rows
        const overdueRows = document.querySelectorAll('.table-danger-light');
        overdueRows.forEach(row => {
            row.style.animation = 'pulseAlert 1.5s infinite';
        });

        // Task search functionality
        const searchInput = document.getElementById('taskSearch');
        const clearButton = document.getElementById('clearSearch');
        const tableRows = document.querySelectorAll('tbody tr');

        // Function to highlight text matches
        function highlightText(element, searchTerm) {
            if (!element || !searchTerm) return;

            const text = element.textContent;
            const lowerText = text.toLowerCase();
            let startIndex = 0;

            // If the search term isn't found in this element, return
            if (!lowerText.includes(searchTerm.toLowerCase())) return;

            // Clear the element's content
            while (element.firstChild) {
                element.removeChild(element.firstChild);
            }

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

        // Function to filter table rows based on search input
        function filterTasks() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const searchFeedback = document.getElementById('searchFeedback');
            const resultCount = document.getElementById('resultCount');
            const searchError = document.getElementById('searchError');
            const noResultsBlock = document.getElementById('noResultsMessage');

            let visibleCount = 0;

            // Remove previous highlights
            document.querySelectorAll('.search-highlight').forEach(el => {
                const parent = el.parentNode;
                parent.replaceChild(document.createTextNode(el.textContent), el);
                parent.normalize();
            });

            tableRows.forEach(row => {
                const taskId = row.querySelector('.fw-semibold')?.textContent || '';
                const issueId = row.querySelector('.fw-medium')?.textContent || '';
                const issueTitle = row.querySelector('small.text-muted')?.textContent || '';
                const technicianName = row.querySelector('td:nth-child(3)')?.textContent || '';
                const assignmentDate = row.querySelector('td:nth-child(4)')?.textContent || '';
                const dueDate = row.querySelector('td:nth-child(5)')?.textContent || '';
                const status = row.querySelector('td:nth-child(6) .badge')?.textContent || '';
                const priority = row.querySelector('td:nth-child(7) .badge')?.textContent || '';

                const matchesSearch =
                    taskId.toLowerCase().includes(searchTerm) ||
                    issueId.toLowerCase().includes(searchTerm) ||
                    issueTitle.toLowerCase().includes(searchTerm) ||
                    technicianName.toLowerCase().includes(searchTerm) ||
                    assignmentDate.toLowerCase().includes(searchTerm) ||
                    dueDate.toLowerCase().includes(searchTerm) ||
                    status.toLowerCase().includes(searchTerm) ||
                    priority.toLowerCase().includes(searchTerm);

                row.style.display = matchesSearch ? '' : 'none';

                if (matchesSearch && searchTerm) {
                    highlightText(row.querySelector('.fw-semibold'), searchTerm);
                    highlightText(row.querySelector('.fw-medium'), searchTerm);
                    highlightText(row.querySelector('small.text-muted'), searchTerm);
                    highlightText(row.querySelector('td:nth-child(3)'), searchTerm);
                    highlightText(row.querySelector('td:nth-child(4) .text-muted'), searchTerm);
                    highlightText(row.querySelector('td:nth-child(5) div'), searchTerm);
                    highlightText(row.querySelector('td:nth-child(6) .badge'), searchTerm);
                    highlightText(row.querySelector('td:nth-child(7) .badge'), searchTerm);
                    visibleCount++;
                }
            });

            // Update feedback UI
            if (searchTerm) {
                resultCount.textContent = visibleCount;
                searchFeedback.style.display = visibleCount > 0 ? 'block' : 'none';
                searchError.style.display = 'none';
                noResultsBlock.style.display = visibleCount === 0 ? 'block' : 'none';
            } else {
                searchFeedback.style.display = 'none';
                searchError.style.display = 'none';
                noResultsBlock.style.display = 'none';
            }
        }

        // Add event listeners
        if (searchInput) {
            searchInput.addEventListener('input', filterTasks);

            // Clear search when X button is clicked
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    filterTasks();

                    // Explicitly hide the search feedback and error message
                    const searchFeedback = document.getElementById('searchFeedback');
                    const searchError = document.getElementById('searchError');
                    if (searchFeedback) {
                        searchFeedback.style.display = 'none';
                    }
                    if (searchError) {
                        searchError.style.display = 'none';
                    }
                });
            }

            // Add keyboard shortcut (Ctrl+F or Cmd+F) to focus search
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        }
    });

    // Add pulse animation for overdue tasks
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulseAlert {
            0% { background-color: rgba(220, 53, 69, 0.03); }
            50% { background-color: rgba(220, 53, 69, 0.08); }
            100% { background-color: rgba(220, 53, 69, 0.03); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
