<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>New Task Assignment - OCM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f4f6fa;
      font-family: 'Poppins', Arial, sans-serif;
      margin: 0;
      padding: 0;
      color: #22223b;
    }
    .email-container {
      max-width: 540px;
      margin: 40px auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(67,97,238,0.08);
      overflow: hidden;
      border: 1px solid #e0e0e0;
    }
    .email-header {
      background: #4361ee;
      color: #fff;
      padding: 32px 32px 16px 32px;
      text-align: center;
    }
    .email-header img {
      height: 40px;
      margin-bottom: 12px;
    }
    .email-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0 0 8px 0;
      letter-spacing: 1px;
    }
    .email-content {
      padding: 32px;
      color: #22223b;
    }
    .email-content p {
      margin: 0 0 18px 0;
      font-size: 1rem;
      line-height: 1.6;
    }
    .panel {
      background: #f4f6fa;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 18px 20px;
      margin: 18px 0;
    }
    .panel h3 {
      margin-top: 0;
      color: #4361ee;
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 10px;
    }
    .task-btn {
      display: inline-block;
      background: #4361ee;
      color: #fff !important;
      text-decoration: none;
      font-weight: 600;
      padding: 14px 32px;
      border-radius: 6px;
      margin: 18px 0;
      font-size: 1rem;
      letter-spacing: 0.5px;
      transition: background 0.2s;
    }
    .task-btn:hover {
      background: #3a56d4;
    }
    .email-footer {
      background: #f4f6fa;
      color: #6c757d;
      text-align: center;
      font-size: 0.95rem;
      padding: 18px 32px;
    }
    .priority-high { color: #e74c3c; font-weight: 600; }
    .priority-medium { color: #f39c12; font-weight: 600; }
    .priority-low { color: #2ecc71; font-weight: 600; }
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 18px;
    }
    .info-table td {
      padding: 6px 0;
      font-size: 1rem;
      vertical-align: top;
    }
    .info-table .label {
      color: #495057;
      font-weight: 600;
      width: 140px;
    }
    .info-table .value {
      color: #22223b;
    }
    @media (max-width: 600px) {
      .email-container, .email-content, .email-header, .email-footer { padding: 16px !important; }
      .panel { padding: 12px 8px; }
      .info-table .label { width: 90px; }
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <div class="email-title">New Task Assignment</div>
      <div style="font-size:1rem; font-weight:500;">Issue #{{ $issue->issue_id }}</div>
    </div>
    <div class="email-content">
      <p>Hello <strong>{{ $technician->first_name }} {{ $technician->last_name }}</strong>,</p>
      <p>
        You have been assigned a new maintenance task in the <strong>OCM</strong> system. Please review the details below and proceed as soon as possible.
      </p>
      <table class="info-table">
        <tr>
          <td class="label">Priority:</td>
          <td class="value">
            @if($task->priority === 'High')
              <span class="priority-high">High</span>
            @elseif($task->priority === 'Medium')
              <span class="priority-medium">Medium</span>
            @else
              <span class="priority-low">Low</span>
            @endif
          </td>
        </tr>
        <tr>
          <td class="label">Expected Completion:</td>
          <td class="value">{{ $task->expected_completion->format('F j, Y g:i A') }}</td>
        </tr>
        <tr>
          <td class="label">Urgency Level:</td>
          <td class="value">{{ $issue->urgency_level }} @if(isset($issue->urgency_score))(Score: {{ $issue->urgency_score }})@endif</td>
        </tr>
        <tr>
          <td class="label">Location:</td>
          <td class="value">{{ $location->building_name }}, Floor {{ $location->floor_number }}, Room {{ $location->room_number }}</td>
        </tr>
        <tr>
          <td class="label">Reporter:</td>
          <td class="value">{{ $reporter->first_name }} {{ $reporter->last_name }}</td>
        </tr>
        <tr>
          <td class="label">Reported At:</td>
          <td class="value">{{ $issue->created_at->format('F j, Y g:i A') }}</td>
        </tr>
      </table>

      <div class="panel">
        <h3>Issue Description</h3>
        <p>{{ $issue->issue_description }}</p>
      </div>

      @if($issue->pc_number)
        <div class="panel">
          <h3>PC Details</h3>
          <ul style="margin:0; padding-left:18px;">
            <li><strong>PC Number:</strong> {{ $issue->pc_number }}</li>
            <li><strong>Issue Type:</strong> {{ $issue->pc_issue_type ?? 'Not specified' }}</li>
            <li><strong>Critical Work Affected:</strong> {{ $issue->critical_work_affected ? 'Yes' : 'No' }}</li>
          </ul>
        </div>
      @endif

      <p style="text-align: center; margin: 30px 0;">
        <a href="{{ $taskUrl }}" class="task-btn">View Full Task Details</a>
      </p>
    </div>
    <div class="email-footer">
      &copy; {{ date('Y') }} OCM - Online Campus Management. All rights reserved.<br>
      <span style="color:#888;">If you are unable to address this issue, please contact your supervisor immediately.</span>
    </div>
  </div>
</body>
</html>
