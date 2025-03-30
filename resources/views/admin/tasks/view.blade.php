@extends('Layouts.AdminNavBar')

@section('content')
    <div class="container task-view-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0 task-header">Task Management</h1>
          
        </div>
        
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
                        <th>Actions</th>
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
                                    @else  @endif">
                                    {{ $tasks->priority }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a 
                                       class="btn btn-info" 
                                       title="View Progress">
                                      View Progress
                                    </a>
                                    @if($tasks->issue_status != 'Completed')
                                    <a 
                                       class="btn btn-warning" 
                                       title="Reassign Task">
                                        Assign Task
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection