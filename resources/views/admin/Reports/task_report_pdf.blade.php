<!DOCTYPE html>
<html>
<head>
    <title>OCM - Maintenance Task Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .system-info {
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .header-info {
            margin-bottom: 20px;
            padding: 10px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            background-color: #f9f9f9;
            display: block;
            text-align: center;
        }
        .header-info span {
            display: inline-block;
            margin: 0 10px;
            padding: 5px 0;
            font-weight: normal;
            color: #555;
        }
        .header-info span strong {
            font-weight: bold;
            color: #34495e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #555;
            font-size: 9px;
            text-transform: uppercase;
        }
        td {
            font-size: 8px;
            color: #444;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: .2em .4em;
            font-size: 70%;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            color: #fff;
            background-color: #6c757d;
        }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; }
        .bg-primary { background-color: #007bff; }
        .bg-danger { background-color: #dc3545; }
        .text-danger-strong { color: #dc3545; font-weight: bold; }
        .alert {
            background-color: #e6f7ff;
            border: 1px solid #b3e0ff;
            color: #336699;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 11px;
            margin-top: 20px;
        }
        .alert i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="system-info">
    OCM - Online Campus Management
</div>
<h1>Maintenance Task Report</h1>
<div class="system-info">
    Generated: <strong>{{ now()->format('F j, Y \a\t H:i A T') }} SAST</strong>
</div>

<div class="header-info">
    <span>Period: <strong>{{ $startDate->format('F j, Y') }}</strong> to <strong>{{ $endDate->format('F j, Y') }}</strong></span>
    <span>Total Tasks: <strong>{{ $stats['total'] }}</strong></span>
    <span>Completed: <strong>{{ $stats['completed'] }}</strong></span>
    <span>Pending: <strong>{{ $stats['pending'] }}</strong></span>
    <span>Overdue: <strong>{{ $stats['overdue'] }}</strong></span>
    <span>Status Filter: <strong>{{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</strong></span>
    <span>Priority Filter: <strong>{{ ucfirst($filters['priority']) }}</strong></span>
</div>

<h2>Task Details</h2>
@if($tasks->isNotEmpty())
    <table>
        <thead>
        <tr>
            <th>Task ID</th>
            <th>Issue Type</th>
            <th>Location</th>
            <th>Assignee</th>
            <th>Status</th>
            <th>Due Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($tasks as $task)
            <tr>
                <td>#{{ $task->task_id }}</td>
                <td>{{ $task->issue->issue_type ?? 'N/A' }}</td>
                <td>
                    @if($task->issue->location)
                        {{ $task->issue->location->building_name }} (Room {{ $task->issue->location->room_number }})
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if($task->assignee)
                        {{ $task->assignee->first_name }} {{ $task->assignee->last_name }}
                    @else
                        Unassigned
                    @endif
                </td>
                <td>
                            <span class="badge
                                @if($task->issue_status == 'completed') bg-success
                                @elseif($task->issue_status == 'pending') bg-warning
                                @elseif($task->issue_status == 'in_progress') bg-primary
                                @else bg-danger
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->issue_status)) }}
                            </span>
                </td>
                <td>
                            <span class="{{ $task->expected_completion < now() && $task->issue_status != 'completed' ? 'text-danger-strong' : '' }}">
                                {{ $task->expected_completion->format('M d, Y') }}
                            </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert">
        <i class="fas fa-info-circle"></i> No tasks found matching your criteria.
    </div>
@endif
</body>
</html>
