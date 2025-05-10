<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3a7bd5;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .stats {
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0;
            color: #3a7bd5;
            font-size: 24px;
        }
        .stat-box p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .priority-high {
            color: #dc3545;
        }
        .priority-medium {
            color: #ffc107;
        }
        .priority-low {
            color: #28a745;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Task Report</h1>
        <p>Period: {{ $start_date }} to {{ $end_date }}</p>
    </div>

    <div class="stats">
        <div class="stats-grid">
            <div class="stat-box">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Tasks</p>
            </div>
            <div class="stat-box">
                <h3>{{ $stats['completed'] }}</h3>
                <p>Completed Tasks</p>
            </div>
            <div class="stat-box">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Pending Tasks</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Expected Completion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $task->task_id }}</td>
                <td>{{ $task->issue->issue_description }}</td>
                <td class="priority-{{ strtolower($task->priority) }}">{{ $task->priority }}</td>
                <td class="status-{{ strtolower($task->issue_status) }}">{{ $task->issue_status }}</td>
                <td>{{ $task->assignee ? $task->assignee->first_name . ' ' . $task->assignee->last_name : 'Unassigned' }}</td>
                <td>{{ $task->expected_completion ? Carbon\Carbon::parse($task->expected_completion)->format('M d, Y') : 'Not set' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>
</body>
</html> 