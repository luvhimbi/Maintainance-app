<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <title>New Task Assignment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    /* Fallback font stack and basic resets */
    body { margin:0; padding:0; font-family: Arial, sans-serif; background-color: #f4f4f4; }
    table { border-collapse: collapse; width: 100%; }
    .container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; }
    .header { padding: 20px; text-align: center; background-color: #2d3748; color: #ffffff; }
    .content { padding: 20px; color: #333333; line-height: 1.5; }
    .panel { background-color: #f9fafb; border: 1px solid #e2e8f0; padding: 15px; margin: 20px 0; }
    .button { display: inline-block; padding: 12px 20px; text-decoration: none; border-radius: 4px; }
    .button-primary { background-color: #3182ce; color: #ffffff; }
    .footer { padding: 20px; text-align: center; font-size: 12px; color: #718096; }
    .priority-high { color: #e74c3c; }
    .priority-medium { color: #f39c12; }
    .priority-low { color: #2ecc71; }
  </style>
</head>
<body>
  <table class="container">
    <tr>
      <td class="header">
        <h1>New Task Assignment</h1>
        <p>Issue #{{ $issue->issue_id }}</p>
      </td>
    </tr>
    <tr>
      <td class="content">
        <p><strong>Technician:</strong> {{ $technician->first_name }} {{ $technician->last_name }}</p>
        <p>
          <strong>Priority:</strong>
          @if($task->priority === 'High')
            <span class="priority-high">High</span>
          @elseif($task->priority === 'Medium')
            <span class="priority-medium">Medium</span>
          @else
            <span class="priority-low">Low</span>
          @endif
        </p>
        <p><strong>Expected Completion:</strong> {{ $task->expected_completion->format('F j, Y g:i A') }}</p>
        <p><strong>Urgency Level:</strong> {{ $issue->urgency_level }} (Score: {{ $issue->urgency_score }})</p>
        <p><strong>Location:</strong> {{ $location->building_name }}, Floor {{ $location->floor_number }}, Room {{ $location->room_number }}</p>
        <p><strong>Reporter:</strong> {{ $reporter->first_name }} {{ $reporter->last_name }}</p>
        <p><strong>Reported At:</strong> {{ $issue->created_at->format('F j, Y g:i A') }}</p>

        <div class="panel">
          <h3>Issue Description</h3>
          <p>{{ $issue->issue_description }}</p>
        </div>

        @if($issue->pc_number)
          <div class="panel">
            <h3>PC Details</h3>
            <ul>
              <li><strong>PC Number:</strong> {{ $issue->pc_number }}</li>
              <li><strong>Issue Type:</strong> {{ $issue->pc_issue_type ?? 'Not specified' }}</li>
              <li><strong>Critical Work Affected:</strong> {{ $issue->critical_work_affected ? 'Yes' : 'No' }}</li>
            </ul>
          </div>
        @endif

        <p style="text-align: center; margin: 30px 0;">
          <a href="{{ $taskUrl }}" class="button button-primary">View Full Task Details</a>
        </p>

     
      </td>
    </tr>
    <tr>
      <td class="footer">
        <p>Thank you,<br>{{ config('app.name') }} Support Team</p>
        <p>If you're unable to address this issue, please contact your supervisor immediately.</p>
      </td>
    </tr>
  </table>
</body>
</html>
