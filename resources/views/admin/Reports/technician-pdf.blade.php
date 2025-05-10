<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Technician Performance Report</title>
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
        .stats {
            margin-bottom: 20px;
        }
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .stat-box {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            width: 48%;
        }
        .stat-label {
            font-weight: bold;
            color: #666;
        }
        .stat-value {
            font-size: 16px;
            color: #3a7bd5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 10px;
        }
        .status-active {
            color: #28a745;
        }
        .status-busy {
            color: #dc3545;
        }
        .status-unknown {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Technician Performance Report</h1>
        <div class="date-range">
            Period: {{ $start_date }} to {{ $end_date }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Technician Name</th>
                <th>Specialization</th>
                <th>Availability</th>
                <th>Current Workload</th>
                <th>Total Tasks</th>
                <th>Completed Tasks</th>
                <th>Completion Rate</th>
                <th>Avg. Completion Time (Days)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($technicians as $technician)
            <tr>
                <td>{{ $technician['name'] }}</td>
                <td>{{ $technician['specialization'] }}</td>
                <td class="status-{{ strtolower($technician['availability']) }}">
                    {{ $technician['availability'] }}
                </td>
                <td>{{ $technician['workload'] }}</td>
                <td>{{ $technician['total_tasks'] }}</td>
                <td>{{ $technician['completed_tasks'] }}</td>
                <td>{{ $technician['completion_rate'] }}%</td>
                <td>{{ number_format($technician['avg_completion_time'], 1) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="stats">
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Total Technicians</div>
                <div class="stat-value">{{ count($technicians) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Average Completion Rate</div>
                <div class="stat-value">
                    {{ number_format($technicians->avg('completion_rate'), 1) }}%
                </div>
            </div>
        </div>
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Average Workload</div>
                <div class="stat-value">
                    {{ number_format($technicians->avg('workload'), 1) }}
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Average Completion Time</div>
                <div class="stat-value">
                    {{ number_format($technicians->avg('avg_completion_time'), 1) }} days
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('F d, Y H:i:s') }}
    </div>
</body>
</html> 