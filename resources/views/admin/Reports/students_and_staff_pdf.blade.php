<!DOCTYPE html>
<html>
<head>
    <title>OCM - Students and Staff Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            -webkit-print-color-adjust: exact; /* For consistent background colors on print */
        }
        h1, h2, h3 {
            color: #2c3e50; /* Darker blue-grey for headings */
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
            color: #7f8c8d; /* Muted grey for system info */
            margin-bottom: 20px;
        }
        .header-info {
            margin-bottom: 20px;
            padding: 10px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            background-color: #f9f9f9; /* Light background for filter info */
            display: block; /* Ensure full width for spacing */
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
            box-shadow: 0 0 5px rgba(0,0,0,0.05); /* Subtle shadow for tables */
        }
        th, td {
            border: 1px solid #e0e0e0; /* Lighter border for cells */
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f5f5f5; /* Lighter header background */
            font-weight: bold;
            color: #555;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            font-size: 9px;
            color: #444;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc; /* Zebra striping for table rows */
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: .3em .6em;
            font-size: 70%;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            color: #fff;
            background-color: #6c757d; /* Default badge color */
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
    <span>Total Students (filtered): <strong>{{ $students->count() }}</strong></span>
    <span>Total Staff (filtered): <strong>{{ $staffMembers->count() }}</strong></span>
    <span>Total Issues Reported (filtered): <strong>{{ $totalStudentIssues + $totalStaffIssues }}</strong></span>
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
                    <span class="badge">{{ $student->issues->count() }}</span>
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
                    <span class="badge">{{ $staff->issues->count() }}</span>
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
