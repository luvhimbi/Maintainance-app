<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Performance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3a7bd5;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #3a7bd5;
            margin: 0;
            font-size: 24px;
        }
        .date-range {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #3a7bd5;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .stat-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            color: #3a7bd5;
        }
        .chart-container {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 10px;
        }
        .priority-high { color: #dc3545; }
        .priority-medium { color: #ffc107; }
        .priority-low { color: #28a745; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Performance Report</h1>
        <div class="date-range">
            Period: {{ $start_date }} to {{ $end_date }}
        </div>
    </div>

    <!-- Task Completion Overview -->
    <div class="section">
        <div class="section-title">Task Completion Overview</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Tasks</th>
                    <th>Completed Tasks</th>
                    <th>Completion Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($performance_data['task_completion'] as $completion)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($completion->date)->format('M d, Y') }}</td>
                    <td>{{ $completion->total }}</td>
                    <td>{{ $completion->completed }}</td>
                    <td>{{ $completion->total > 0 ? round(($completion->completed / $completion->total) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Priority Distribution -->
    <div class="section">
        <div class="section-title">Priority Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Priority</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTasks = $performance_data['priority_distribution']->sum('count');
                @endphp
                @foreach($performance_data['priority_distribution'] as $priority)
                <tr>
                    <td class="priority-{{ strtolower($priority->priority) }}">
                        {{ ucfirst($priority->priority) }}
                    </td>
                    <td>{{ $priority->count }}</td>
                    <td>{{ $totalTasks > 0 ? round(($priority->count / $totalTasks) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Issue Types -->
    <div class="section">
        <div class="section-title">Issue Types Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Issue Type</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalIssues = $performance_data['issue_types']->sum('count');
                @endphp
                @foreach($performance_data['issue_types'] as $issue)
                <tr>
                    <td>{{ $issue->issue_type }}</td>
                    <td>{{ $issue->count }}</td>
                    <td>{{ $totalIssues > 0 ? round(($issue->count / $totalIssues) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Technician Performance -->
    <div class="section">
        <div class="section-title">Technician Performance</div>
        <table>
            <thead>
                <tr>
                    <th>Technician</th>
                    <th>Specialization</th>
                    <th>Total Tasks</th>
                    <th>Completed Tasks</th>
                    <th>Completion Rate</th>
                    <th>Current Workload</th>
                </tr>
            </thead>
            <tbody>
                @foreach($performance_data['technician_performance'] as $technician)
                <tr>
                    <td>{{ $technician['name'] }}</td>
                    <td>{{ $technician['specialization'] }}</td>
                    <td>{{ $technician['total_tasks'] }}</td>
                    <td>{{ $technician['completed_tasks'] }}</td>
                    <td>{{ $technician['completion_rate'] }}%</td>
                    <td>{{ $technician['current_workload'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary Statistics -->
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total Tasks</div>
                <div class="stat-value">{{ $performance_data['task_completion']->sum('total') }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Completed Tasks</div>
                <div class="stat-value">{{ $performance_data['task_completion']->sum('completed') }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Average Completion Rate</div>
                <div class="stat-value">
                    @php
                        $totalTasks = $performance_data['task_completion']->sum('total');
                        $totalCompleted = $performance_data['task_completion']->sum('completed');
                        $avgRate = $totalTasks > 0 ? round(($totalCompleted / $totalTasks) * 100, 1) : 0;
                    @endphp
                    {{ $avgRate }}%
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Active Technicians</div>
                <div class="stat-value">{{ count($performance_data['technician_performance']) }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('F d, Y H:i:s') }}
    </div>
</body>
</html> 