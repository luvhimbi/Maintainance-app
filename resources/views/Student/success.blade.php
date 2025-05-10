<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Reported Successfully</title>
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
        
        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--success-color);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }
        
        .success-title {
            color: var(--success-color);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.15);
            color: #2a8c5a;
            border-left: 4px solid var(--success-color);
        }
        
        .tracking-box {
            background-color: white;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .tracking-box p {
            color: #495057;
            margin-bottom: 1rem;
        }
        
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
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
        
        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
        }
        
        .step.completed .step-label {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        @media (min-width: 576px) {
            .button-container {
                flex-direction: row;
                justify-content: center;
            }
            
            .button-container .btn {
                min-width: 200px;
            }
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

@extends('Layouts.StudentNavbar')

@section('title', 'Issue Reported Successfully')

@section('content')
    <div class="form-container">
        <div class="progress-steps mb-4">
            <div class="step completed">
                <div class="step-number">1</div>
                <div class="step-label">Create Report</div>
            </div>
            <div class="step completed">
                <div class="step-number">2</div>
                <div class="step-label">Confirm Details</div>
            </div>
            <div class="step completed">
                <div class="step-number">3</div>
                <div class="step-label">Submission Complete
                </div>
            </div>
        </div>
        
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h3 class="success-title">Issue Reported Successfully!</h3>
        
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success!</strong> Your issue has been reported successfully and is now in our system.
        </div>
        
        <div class="tracking-box">
            <h5 class="mb-3">Issue Details:</h5>
            <div class="issue-details">
                <p><strong>Issue ID:</strong> #{{ session('issue_id') }}</p>
                @if(session('assigned_technician'))
                    <p><strong>Assigned Technician:</strong> {{ session('assigned_technician') }}</p>
                @else
                    <p><strong>Status:</strong> Awaiting technician assignment</p>
                @endif
            </div>
            
            <div class="button-container mt-4">
                <a href="{{ route('Student.issue_details', ['id' => session('issue_id')]) }}" 
                   class="btn btn-primary">
                    <i class="fas fa-search me-2"></i> View Issue Details
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i> Return to Home
                </a>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted">
            <p class="mb-1">Thank you for reporting this issue.</p>
            <p class="small">Our maintenance team will review your report as soon as possible.</p>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>