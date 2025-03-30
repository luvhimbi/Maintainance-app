@extends('Layouts.AdminNavBar')

@section('content')
    <div class="container task-view-container">
        <h1 class="mb-4 task-header">Task Management</h1>
        
        <!-- Information Alert -->
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle"></i> If the system shows "N/A" for Admin, it means the task was automatically assigned based on technician availability.
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Task ID</th>
                        <th>Issue ID</th>
                        <th>Assignee</th>
                        <th>Admin</th>
                        <th>Assignment Date</th>
                        <th>Expected Completion</th>
                        <th>Status</th>
                        <th>Priority</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task as $tasks)
                        <tr>
                            <td>{{ $tasks->task_id }}</td>
                            <td>{{ $tasks->issue_id }}</td>
                            <td>
                                @if($tasks->assignee)
                                    {{ $tasks->assignee->username }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($tasks->admin)
                                    {{ $tasks->admin->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $tasks->assignment_date }}</td>
                            <td>{{ $tasks->expected_completion }}</td>
                            <td>
                                <span class="badge 
                                    @if($tasks->issue_status == 'Completed') badge-success
                                    @elseif($tasks->issue_status == 'In Progress') badge-primary
                                    @elseif($tasks->issue_status == 'Pending') badge-warning
                                    @else badge-secondary @endif">
                                    {{ $tasks->issue_status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($tasks->priority == 'High') badge-danger
                                    @elseif($tasks->priority == 'Medium') badge-warning
                                    @else badge-info @endif">
                                    {{ $tasks->priority }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .task-view-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .task-header {
            color: #343a40;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            font-weight: 600;
        }
        
        .table {
            margin-top: 20px;
        }
        
        .table th {
            background-color: #495057;
            color: white;
            font-weight: 500;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .badge {
            font-size: 0.9em;
            padding: 5px 10px;
            border-radius: 5px;
        }
       
        .alert-info {
            background-color: #e7f5ff;
            border-color: #a5d8ff;
            color: #1862ab;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border: 0;
            }
            
            .table thead {
                display: none;
            }
            
            .table tr {
                margin-bottom: 15px;
                display: block;
                border: 1px solid #dee2e6;
                border-radius: 5px;
            }
            
            .table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #dee2e6;
            }
            
            .table td:before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 15px;
            }
        }
    </style>
@endsection