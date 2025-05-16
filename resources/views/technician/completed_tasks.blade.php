@extends('Layouts.TechnicianNavbar')
@section('title', 'Completed Tasks')
@section('content')
    <div class="container mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Completed Tasks</h1>
                <p class="text-muted mb-0">View All Completed Maintenance Tasks</p>
            </div>
            <div class="bg-light p-3 rounded">
                <span class="fw-bold">{{ $completedTasks->count() }}</span> completed tasks
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
                            <input type="text" id="taskSearch" class="form-control border-start-0 ps-0"
                                   placeholder="Search by issue type or location...">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <select class="form-select form-select-lg" id="priorityFilter">
                            <option value="">All Priority Levels</option>
                            <option value="Low">Low Priority</option>
                            <option value="Medium">Medium Priority</option>
                            <option value="High">High Priority</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        @if($completedTasks->count() > 0)
            <div class="card shadow-sm border-0 rounded-3 hover-lift">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Issue Type</th>
                            <th class="py-3">Priority</th>
                            <th class="py-3">Completion Time</th>
                            <th class="py-3">Duration</th>
                            <th class="py-3">Location</th>
                            <th class="text-end pe-4 py-3">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="tasksTableBody">
                        @foreach ($completedTasks as $task)
                            <tr class="border-bottom">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @php
                                            $iconClass = match($task->issue->issue_type) {
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
                                        <div>
                                            <span class="fw-medium d-block">{{ $task->issue->issue_type }}</span>
                                            <small class="text-muted">Task #{{ $task->task_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($task->priority == 'Low') bg-success-subtle text-success
                                        @elseif($task->priority == 'Medium') bg-warning-subtle text-warning
                                        @elseif($task->priority == 'High') bg-danger-subtle text-danger
                                        @endif px-3 py-2">
                                        {{ $task->priority }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">
            @if($task->actual_completion)
                                                {{ $task->actual_completion->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
        </span>
                                        <small class="text-muted">
                                            @if($task->actual_completion)
                                                {{ $task->actual_completion->format('h:i A') }}
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $duration = $task->created_at->diff($task->actual_completion);
                                        $durationString = '';
                                        if ($duration->d > 0) $durationString .= $duration->d . 'd ';
                                        if ($duration->h > 0) $durationString .= $duration->h . 'h ';
                                        if ($duration->i > 0) $durationString .= $duration->i . 'm';
                                    @endphp
                                    <span class="badge bg-light text-dark">
                                        {{ $durationString ?: 'Less than 1m' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-light-secondary me-2">
                                            <i class="fas fa-map-marker-alt text-secondary"></i>
                                        </div>
                                        <div>
                                            <span class="d-block">{{ $task->issue->location->building_name ?? 'N/A' }}</span>
                                            <small class="text-muted">Room {{ $task->issue->location->room_number ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('tasks.updates', $task->task_id) }}"
                                           class="btn btn-primary btn-sm px-3"
                                           data-bs-toggle="tooltip" title="View Update Log">
                                            <i class="fas fa-history"></i>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card shadow-sm border-0 rounded-3 hover-lift">
                <div class="card-body py-5">
                    <div class="text-center">
                        <div class="empty-state-icon mb-4">
                            <i class="fas fa-check-circle fa-4x text-success-subtle"></i>
                        </div>
                        <h4 class="fw-bold mb-2">No Completed Tasks Found</h4>
                        <p class="text-muted mb-4">Tasks you mark as completed will appear here</p>
                        <a href="{{ route('technician.dashboard') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-tasks me-1"></i> View Active Tasks
                        </a>
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
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(tooltipTriggerEl => {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const searchInput = document.getElementById('taskSearch');
            const priorityFilter = document.getElementById('priorityFilter');
            const tableBody = document.getElementById('tasksTableBody');

            function filterTasks() {
                const searchTerm = searchInput.value.toLowerCase();
                const priorityValue = priorityFilter.value;
                const rows = tableBody.getElementsByTagName('tr');

                Array.from(rows).forEach(row => {
                    const issueType = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    const taskId = row.querySelector('td:nth-child(1) small').textContent.toLowerCase();
                    const priority = row.querySelector('td:nth-child(2)').textContent.trim();
                    const location = row.querySelector('td:nth-child(5) span:first-child').textContent.toLowerCase();

                    const matchesSearch = issueType.includes(searchTerm) ||
                        location.includes(searchTerm) ||
                        taskId.includes(searchTerm);
                    const matchesPriority = !priorityValue || priority.includes(priorityValue);

                    row.style.display = matchesSearch && matchesPriority ? '' : 'none';
                });
            }

            // Add debounce to search
            let searchTimeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(filterTasks, 300);
            });

            priorityFilter.addEventListener('change', filterTasks);
        });
    </script>
@endsection
