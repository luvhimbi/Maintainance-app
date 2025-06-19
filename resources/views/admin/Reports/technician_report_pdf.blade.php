<!DOCTYPE html>
<html>
<head>
    <title>OCM - Technician Performance Report</title>
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
        h2 {
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
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
<h1>Technician Performance Report</h1>
<div class="system-info">
    Generated: <strong>{{ now()->format('F j, Y \a\t H:i A T') }} SAST</strong>
</div>

<div class="header-info">
    <span>Period: <strong>{{ $startDate->format('F j, Y') }}</strong> to <strong>{{ $endDate->format('F j, Y') }}</strong></span>
    <span>Total Technicians (filtered): <strong>{{ $stats['total_technicians'] }}</strong></span>
    <span>Total Tasks Assigned (filtered): <strong>{{ $stats['total_tasks'] }}</strong></span>
    <span>Total Tasks Completed (filtered): <strong>{{ $stats['completed_tasks'] }}</strong></span>
    <span>Average Completion Rate: <strong>{{ number_format($stats['avg_completion_rate'], 2) }}%</strong></span>
    <span>Status Filter: <strong>{{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</strong></span>
    <span>Priority Filter: <strong>{{ ucfirst($filters['priority']) }}</strong></span>
</div>

<h2>Technician Details</h2>
@if($technicians->isNotEmpty())
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Specialization</th>
            <th>Availability</th>
            <th>Workload</th>
            <th>Total Tasks</th>
            <th>Completed Tasks</th>
            <th>Completion Rate</th>
            <th>Avg Completion Time (Days)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($technicians as $technician)
            <tr>
                <td>{{ $technician['name'] }}</td>
                <td>{{ $technician['specialization'] }}</td>
                <td>{{ $technician['availability'] }}</td>
                <td>{{ $technician['workload'] }}</td>
                <td>{{ $technician['total_tasks'] }}</td>
                <td>{{ $technician['completed_tasks'] }}</td>
                <td>{{ number_format($technician['completion_rate'], 2) }}%</td>
                <td>{{ number_format($technician['avg_completion_time'], 1) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert">
        <i class="fas fa-info-circle"></i> No technicians found matching your criteria.
    </div>
@endif
</body>
</html>
