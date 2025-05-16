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
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-primary {
            background-color: #3a7bd5;
            color: white;
            margin-right: 5px;
        }
        .filters {
            margin-bottom: 15px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Technician Performance Report</h1>
        {{-- <div class="date-range">
            Period: {{ $startDate }} to {{ $endDate }}
        </div> --}}
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Filters Applied:</strong>
        @if($filters['status'] != 'all') <span class="badge badge-primary">Status: {{ ucfirst($filters['status']) }}</span> @endif
        @if($filters['priority'] != 'all') <span class="badge badge-primary">Priority: {{ ucfirst($filters['priority']) }}</span> @endif
        @if($filters['specialization'] != 'all') <span class="badge badge-primary">Specialization: {{ $filters['specialization'] }}</span> @endif
        @if($filters['technician_id'] != 'all') 
            @php
                $technicianName = collect($technicians)->firstWhere('id', $filters['technician_id'])['name'] ?? 'N/A';
            @endphp
            <span class="badge badge-primary">Technician: {{ $technicianName }}</span>
        @endif
    </div>
    @endif

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
            @forelse($technicians as $technician)
            <tr>
                <td>{{ $technician['name'] }}</td>
                <td>{{ $technician['specialization'] }}</td>
                <td>
                    @if(strtolower($technician['availability']) == 'available')
                        <span class="badge badge-success">{{ $technician['availability'] }}</span>
                    @elseif(strtolower($technician['availability']) == 'busy')
                        <span class="badge badge-danger">{{ $technician['availability'] }}</span>
                    @else
                        <span class="badge badge-warning">{{ $technician['availability'] }}</span>
                    @endif
                </td>
                <td>{{ $technician['workload'] }}</td>
                <td>{{ $technician['total_tasks'] }}</td>
                <td>{{ $technician['completed_tasks'] }}</td>
                <td>{{ $technician['completion_rate'] }}%</td>
                <td>{{ $technician['avg_completion_time'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No technicians found for the selected filters and date range.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y H:i:s') }}
    </div>
</body>
</html>