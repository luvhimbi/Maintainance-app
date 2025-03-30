<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Tasks</title>
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #3498db;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-title {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            margin-right: 0.75rem;
            color: var(--primary-color);
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-content h4 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .stat-content p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        
        .tasks-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .tasks-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .tasks-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0;
        }
        
        .tasks-filter {
            display: flex;
            gap: 0.5rem;
        }
        
        .filter-btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid #e0e0e0;
            background-color: white;
            color: #6c757d;
            transition: all 0.2s ease;
        }
        
        .filter-btn.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .filter-btn:hover:not(.active) {
            background-color: #f8f9fa;
        }
        
        .tasks-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .tasks-table th {
            background-color: #f8f9fa;
            padding: 1rem;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        
        .tasks-table td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            color: #212529;
        }
        
        .tasks-table tr:last-child td {
            border-bottom: none;
        }
        
        .tasks-table tr {
            transition: all 0.2s ease;
        }
        
        .tasks-table tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-pending {
            background-color: rgba(243, 156, 18, 0.15);
            color: #9a6400;
        }
        
        .status-progress {
            background-color: rgba(67, 97, 238, 0.15);
            color: #3048a7;
        }
        
        .status-completed {
            background-color: rgba(46, 204, 113, 0.15);
            color: #1e8449;
        }
        
        .priority-low {
            background-color: rgba(52, 152, 219, 0.15);
            color: #2874a6;
        }
        
        .priority-medium {
            background-color: rgba(243, 156, 18, 0.15);
            color: #9a6400;
        }
        
        .priority-high {
            background-color: rgba(231, 76, 60, 0.15);
            color: #a93226;
        }
        
        .btn-action {
            padding: 0.5rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            color: #495057;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
        }
        
        .btn-action:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .action-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .empty-state {
            padding: 3rem;
            text-align: center;
        }
        
        .empty-icon {
            font-size: 3rem;
            color: #e0e0e0;
            margin-bottom: 1.5rem;
        }
        
        .empty-text {
            color: #6c757d;
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
        }
        
        .pagination {
            margin-top: 1.5rem;
            justify-content: center;
        }
        
        .page-link {
            border-radius: 8px;
            margin: 0 0.25rem;
            color: var(--primary-color);
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        @media (max-width: 992px) {
            .tasks-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: 1fr;
            }
            
            .tasks-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .tasks-filter {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>

@extends('Layouts.TechnicianNavBar')

@section('title', 'Assigned Tasks')

@section('content')
    <div class="dashboard-container">
        <h1 class="page-title">
             Assigned Tasks
        </h1>
        
        
        
        <!-- Tasks Container -->
        <div class="tasks-container">
            <div class="tasks-header">
                <h2 class="tasks-title">Task List</h2>
                
               
            </div>
            
            @if ($tasks->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="empty-text">No tasks have been assigned to you yet</div>
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-2"></i> Refresh
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="tasks-table">
                        <thead>
                            <tr>
                                <th>Task ID</th>
                                <th>Issue ID</th>
                                <th>Assignment Date</th>
                                <th>Expected Completion</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ $task->task_id }}</td>
                                    <td>{{ $task->issue_id }}</td>
                                    <td>{{ $task->assignment_date->format('Y-m-d H:i') }}</td>
                                    <td>{{ $task->expected_completion->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="status-badge 
                                            @if ($task->issue_status === 'Pending')
                                                status-pending
                                            @elseif ($task->issue_status === 'In Progress')
                                                status-progress
                                            @elseif ($task->issue_status === 'Completed')
                                                status-completed
                                            @endif">
                                            {{ $task->issue_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge 
                                            @if ($task->priority === 'Low')
                                                priority-low
                                            @elseif ($task->priority === 'Medium')
                                                priority-medium
                                            @elseif ($task->priority === 'High')
                                                priority-high
                                            @endif">
                                            {{ $task->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('technician.task_details', ['task_id' => $task->task_id]) }}" class="btn-action" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
               
            @endif
        </div>
    </div>
@endsection


</body>     
</html>