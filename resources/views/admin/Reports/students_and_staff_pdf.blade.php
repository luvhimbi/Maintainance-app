<!DOCTYPE html>
<html>
<head>
    <title>OCM - Students and Staff Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2c3e50;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            margin: 20px;
        }

        h1, h2 {
            margin-bottom: 10px;
            color: #1a252f;
        }

        h1 {
            font-size: 24px;
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 8px;
        }

        h2 {
            font-size: 18px;
            margin-top: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .system-info, .header-info {
            text-align: center;
            color: #555;
            margin-bottom: 15px;
        }

        .header-info span {
            display: inline-block;
            margin: 0 8px;
            font-size: 11px;
            color: #444;
        }

        .header-info span strong {
            color: #1a252f;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: #fdfdfd;
            box-shadow: 0 0 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 10px 8px;
            border: 1px solid #e0e0e0;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f4f6f7;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 12px;
            color: #fff;
        }


        .badge-default {
            background-color: #7f8c8d;
            font: 10px;
        }

        .alert {
            background-color: #fef9e7;
            border: 1px solid #f7dc6f;
            color: #7d6608;
            padding: 12px;
            text-align: center;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 11px;
        }

        .alert i {
            margin-right: 6px;
        }
    </style>
</head>
<body>
<div class="system-info">
    <strong>OCM - Online Campus Management</strong>
</div>

<h1>Students & Staff Report</h1>

<div class="system-info">
    Report Generated: <strong>{{ now()->format('F j, Y \a\t H:i') }} SAST</strong>
</div>

<div class="header-info">
    @if($startDate && $endDate)
        <span>Date Range: <strong>{{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</strong></span>
    @endif
    @if($searchTerm)
        <span>Search Term: <strong>"{{ $searchTerm }}"</strong></span>
    @endif
    <span>Total Students: <strong>{{ $students->count() }}</strong></span>
    <span>Total Staff: <strong>{{ $staffMembers->count() }}</strong></span>
    <span>Total Issues: <strong>{{ $totalStudentIssues + $totalStaffIssues }}</strong></span>
</div>

<h2>Students Overview</h2>

@if($students->isNotEmpty())
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Student No.</th>
            <th>Course</th>
            <th class="text-center">Issues Reported</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $student)
            <tr>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->studentDetail->student_number ?? 'N/A' }}</td>
                <td>{{ $student->studentDetail->course ?? 'N/A' }}</td>
                <td class="text-center">
                    <span class="badge badge-default">{{ $student->issues->count() }}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert">
        <i class="fas fa-info-circle"></i> No student accounts found matching your criteria.
    </div>
@endif

<h2>Staff Members Overview</h2>

@if($staffMembers->isNotEmpty())
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Position</th>
            <th class="text-center">Issues Reported</th>
        </tr>
        </thead>
        <tbody>
        @foreach($staffMembers as $staff)
            <tr>
                <td>{{ $staff->first_name }} {{ $staff->last_name }}</td>
                <td>{{ $staff->email }}</td>
                <td>{{ $staff->staffDetail->department ?? 'N/A' }}</td>
                <td>{{ $staff->staffDetail->position_title ?? 'N/A' }}</td>
                <td class="text-center">
                    <span class="badge badge-default">{{ $staff->issues->count() }}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert">
        <i class="fas fa-info-circle"></i> No staff member accounts found matching your criteria.
    </div>
@endif
</body>
</html>
