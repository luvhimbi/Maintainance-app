{{-- PDF Template --}}
{{-- filepath: resources/views/admin/reports/pdf/task_report.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <title>Task Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #7f8c8d;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            margin: 1.5cm;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 15px;
        }

        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 14px;
            color: var(--secondary-color);
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-card {
            flex: 1;
            padding: 15px;
            margin: 0 10px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th {
            background: var(--primary-color);
            color: white;
            padding: 12px;
            font-weight: 600;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .priority-high {
            background-color: #ffeaea;
            border-left: 4px solid #e74c3c;
        }

        .priority-medium {
            background-color: #fff5e6;
            border-left: 4px solid #f39c12;
        }

        .priority-low {
            background-color: #e8f5e9;
            border-left: 4px solid #2ecc71;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bg-success { background: #27ae60; }
        .bg-warning { background: #f1c40f; }
        .bg-secondary { background: #95a5a6; }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
            font-size: 10px;
            color: var(--secondary-color);
            text-align: right;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .logo {
            height: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="logo-container">
        <img src="{{ storage_path('images/images.png') }}" class="logo" alt="Company Logo">
    </div>
    <div class="report-title">Maintenance Task Report</div>
    <div class="report-period">
        {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
    </div>
</div>

<div class="stats-container">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Tasks</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['completed'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['in_progress'] }}</div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['high_priority'] }}</div>
        <div class="stat-label">High Priority</div>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Task Type</th>
        <th>Assignee</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Due Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($tasks as $task)
        <tr class="priority-{{ $task->priority }}">
            <td>{{ $task->task_id }}</td>
            <td>{{ $task->issue->issue_type }}</td>
            <td>{{ $task->assignee ? $task->assignee->full_name : 'Unassigned' }}</td>
            <td>
                    <span class="badge
                        @if($task->issue_status == 'Completed') bg-success
                        @elseif($task->issue_status == 'In Progress') bg-warning
                        @else bg-secondary
                        @endif">
                        {{ $task->issue_status }}
                    </span>
            </td>
            <td>{{ ucfirst($task->priority) }}</td>
            <td>{{ $task->expected_completion ? $task->expected_completion->format('M d, Y') : 'N/A' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    Generated on {{ now()->format('M d, Y \a\t H:i') }} |
    {{ config('app.name') }} Maintenance System
</div>
</body>
</html>
