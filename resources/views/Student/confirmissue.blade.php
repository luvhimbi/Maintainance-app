<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #343a40;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .issue-info {
            background-color: white;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .issue-info:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .info-item {
            display: flex;
            margin-bottom: 0.75rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        
        .info-value {
            color: #212529;
        }
        
        .attachment-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border: 1px solid #e0e0e0;
        }
        
        .attachment-item i {
            color: var(--primary-color);
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }
        
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #e0e0e0;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 20px;
            left: 40px;
            width: calc(100% - 40px);
            height: 2px;
            background-color: #e0e0e0;
            z-index: 0;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .step.completed .step-number {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }
        
        .step.active .step-number {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .step.completed .step-label {
            color: var(--success-color);
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>

@extends('layouts.StudentNavbar')

@section('title', 'Confirm Issue Report')

@section('content')
    <div class="form-container">
        <h3 class="text-center mb-4">Confirm Issue Report</h3>
        
        <div class="progress-steps mb-4">
            <div class="step completed">
                <div class="step-number">1</div>
                <div class="step-label">Create Report</div>
            </div>
            <div class="step active">
                <div class="step-number">2</div>
                <div class="step-label">Review & Confirm</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Submitted</div>
            </div>
        </div>
        
        <div class="issue-info">
            <div class="section-title">
                <i class="fas fa-info-circle"></i> Issue Details
            </div>
            <div class="info-item">
                <div class="info-label">Reporter ID:</div>
                <div class="info-value">{{ $formData['reporter_id'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $location->building_name }} - Floor {{ $location->floor_number }}, Room {{ $location->room_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Issue Type:</div>
                <div class="info-value">{{ $formData['issue_type'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Urgency Level:</div>
                <div class="info-value">
                    @if($formData['urgency_level'] == 'high')
                        <span class="badge bg-danger">High</span>
                    @elseif($formData['urgency_level'] == 'medium')
                        <span class="badge bg-warning text-dark">Medium</span>
                    @else
                        <span class="badge bg-success">Low</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Description:</div>
                <div class="info-value">{{ $formData['issue_description'] }}</div>
            </div>
        </div>
        
        <div class="issue-info">
            <div class="section-title">
                <i class="fas fa-paperclip"></i> Attachments
            </div>
            @if (count($attachments) > 0)
                @foreach ($attachments as $attachment)
                    <div class="attachment-item">
                        <i class="fas fa-file"></i>
                        <div>
                            <div>{{ $attachment['original_name'] }}</div>
                            <small class="text-muted">{{ number_format($attachment['file_size'] / 1024, 2) }} KB</small>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">No attachments uploaded.</p>
            @endif
        </div>
        
        <div class="d-grid gap-3 mt-4">
            <form action="{{ route('issue.save') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-check-circle me-2"></i>Confirm and Submit
                </button>
            </form>
            <a href="{{ route('issue.edit') }}" class="btn btn-outline-secondary w-100">
                <i class="fas fa-edit me-2"></i>Go Back and Edit
            </a>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>