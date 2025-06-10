<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Task Reassignment Notice - OCM</title>
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
      background: #f39c12;
      color: #fff;
      padding: 32px 32px 16px 32px;
      text-align: center;
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
      color: #f39c12;
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 10px;
    }
    .email-footer {
      background: #f4f6fa;
      color: #6c757d;
      text-align: center;
      font-size: 0.95rem;
      padding: 18px 32px;
    }
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
      <div class="email-title">Task Reassignment Notice</div>
      <div style="font-size:1rem; font-weight:500;">Issue #{{ $issue->issue_id }}</div>
    </div>
    <div class="email-content">
      <p>Hello <strong>{{ $technician->first_name }} {{ $technician->last_name }}</strong>,</p>
      <p>
        This is to inform you that you have been <span style="color:#f39c12; font-weight:600;">unassigned</span> from the following maintenance issue in the <strong>OCM</strong> system.
      </p>
      <table class="info-table">
        <tr>
          <td class="label">Previous Issue Type:</td>
          <td class="value">{{ $oldType }}</td>
        </tr>
        <tr>
          <td class="label">New Issue Type:</td>
          <td class="value">{{ $newType }}</td>
        </tr>
        <tr>
          <td class="label">Location:</td>
          <td class="value">{{ $issue->location->building_name ?? 'N/A' }}, Room {{ $issue->location->room_number ?? 'N/A' }}</td>
        </tr>
        <tr>
          <td class="label">Urgency:</td>
          <td class="value">{{ $issue->urgency_level }}</td>
        </tr>
        <tr>
          <td class="label">Status:</td>
          <td class="value">{{ $issue->issue_status }}</td>
        </tr>
      </table>

      <div class="panel">
        <h3>Issue Description</h3>
        <p>{{ $issue->issue_description }}</p>
      </div>

      <p style="color:#f39c12; font-weight:500; text-align:center;">
        You are no longer responsible for this issue. If you have any questions, please contact your supervisor.
      </p>
    </div>
    <div class="email-footer">
      &copy; {{ date('Y') }} OCM - Online Campus Management. All rights reserved.<br>
      <span style="color:#888;">If you have any questions, please contact support.</span>
    </div>
  </div>
</body>
</html>
