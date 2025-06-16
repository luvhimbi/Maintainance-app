<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .filters p {
            margin: 5px 0;
        }
        .stats {
            margin-bottom: 20px;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .stats-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .stats-table td:first-child {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .tasks-table {
            width: 100%;
            border-collapse: collapse;
        }
        .tasks-table th {
            background-color: #f8f9fa;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .tasks-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .status-completed {
            color: #28a745;
        }
        .status-pending {
            color: #ffc107;
        }
        .status-in-progress {
            color: #17a2b8;
        }
        .status-overdue {
            color: #dc3545;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Task Report</h1>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    <div class="filters">
        <p><strong>Date Range:</strong> {{ $startDate->format('F d, Y') }} to {{ $endDate->format('F d, Y') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($filters['status']) }}</p>
        <p><strong>Priority:</strong> {{ ucfirst($filters['priority']) }}</p>
    </div>

    <div class="stats">
        <table class="stats-table">
            <tr>
                <td>Total Tasks</td>
                <td>{{ $stats['total'] }}</td>
            </tr>
            <tr>
                <td>Completed</td>
                <td>{{ $stats['completed'] }}</td>
            </tr>
            <tr>
                <td>Pending</td>
                <td>{{ $stats['pending'] }}</td>
            </tr>
            <tr>
                <td>Overdue</td>
                <td>{{ $stats['overdue'] }}</td>
            </tr>
        </table>
    </div>

    <table class="tasks-table">
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Issue Type</th>
                <th>Building</th>
                <th>Floor</th>
                <th>Room</th>
                <th>Assignee</th>
                <th>Status</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td>#{{ $task->task_id }}</td>
                    <td>{{ $task->issue->issue_type ?? 'N/A' }}</td>
                    <td>{{ $task->issue->building->building_name ?? 'N/A' }}</td>
                    <td>{{ $task->issue->floor->floor_number ?? 'N/A' }}</td>
                    <td>{{ $task->issue->room->room_number ?? 'N/A' }}</td>
                    <td>
                        @if($task->assignee)
                            {{ $task->assignee->first_name }} {{ $task->assignee->last_name }}
                        @else
                            Unassigned
                        @endif
                    </td>
                    <td class="status-{{ strtolower(str_replace(' ', '-', $task->issue_status)) }}">
                        {{ ucfirst(str_replace('_', ' ', $task->issue_status)) }}
                    </td>
                    <td>{{ $task->expected_completion->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No tasks found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the system.</p>
        <p>© {{ date('Y') }} TUT Maintenance System. All rights reserved.</p>
    </div>
</body>
</html> 