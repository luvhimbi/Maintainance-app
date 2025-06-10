<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Reported Successfully</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Custom CSS variables for consistent theming */
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --text-dark: #212529;
            --text-medium: #495057;
            --text-light: #6c757d;
            --border-color: #e0e0e0;
            --bg-light: #f8f9fa;
        }

        /* Base body styling */
        body {
            font-family: 'Inter', sans-serif; /* Apply Inter font */
            background-color: var(--bg-light); /* Light gray background */
            color: var(--text-dark);
        }

        /* Main container styling - consistent with other pages */
        .form-container { /* Renamed from previous 'form-container' to be more generic for content */
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
        }

        /* Success Icon Styling */
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
            box-shadow: 0 0 0 8px rgba(46, 204, 113, 0.2); /* Subtle glow */
        }

        /* Success Title Styling */
        .success-title {
            color: var(--success-color);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 700; /* Bolder for impact */
            font-size: 2.25rem; /* Larger font size */
        }

        /* Alert Styling - consistent with other pages */
        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none; /* Remove default Bootstrap border */
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.15); /* Light green background */
            color: #2a8c5a; /* Darker green text */
            border-left: 4px solid var(--success-color); /* Green left border */
        }

        /* Tracking Box Styling - consistent with card design */
        .tracking-box {
            background-color: white;
            border-radius: 12px;
            border: 1px solid var(--border-color); /* Consistent border */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03); /* Lighter shadow */
            padding: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .tracking-box h5 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* Info Item Styling - consistent with confirm page */
        .info-item {
            display: flex;
            align-items: center;
            justify-content: center; /* Center content in tracking box */
            margin-bottom: 0.75rem; /* Slightly less space than form inputs */
        }

        .info-item strong.info-label {
            display: inline-block;
            font-weight: 600;
            color: var(--text-medium);
            min-width: 150px; /* Align labels */
            text-align: right; /* Align labels to the right */
            padding-right: 1rem;
        }

        .info-item .info-value {
            color: var(--text-dark);
            text-align: left; /* Align values to the left */
            flex-grow: 1;
        }

        /* Button Styling - consistent with other pages */
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none; /* Remove default Bootstrap border */
            cursor: pointer;
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
            color: var(--text-medium);
            border: 1px solid var(--border-color);
            background-color: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* Progress Steps Styling - consistent with other pages */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
            width: 100%;
            max-width: 600px; /* Constrain width for aesthetics */
            margin-left: auto;
            margin-right: auto;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--success-color); /* Line is green because all steps are completed */
            z-index: 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--success-color); /* All completed */
            border: 2px solid var(--success-color); /* All completed */
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.2); /* Subtle glow for completed */
        }

        .step-label {
            font-size: 0.85rem;
            color: var(--success-color); /* All completed */
            font-weight: 600;
            text-align: center;
            transition: color 0.3s ease;
        }

        /* Button Container for responsiveness */
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem; /* Spacing from tracking box content */
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

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            .success-title {
                font-size: 1.75rem;
            }
            .info-item {
                flex-direction: column; /* Stack label and value */
                align-items: flex-start;
            }
            .info-item strong.info-label {
                text-align: left;
                min-width: auto;
                padding-right: 0;
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>

@extends('layouts.StudentNavbar')

@section('title', 'Issue Reported Successfully')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">


                <div class="form-container">
                    <div class="text-center mb-4">
                        <div class="success-icon mb-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3 class="fw-bold mb-2 success-title" style="color:#2ecc71;">Issue Reported Successfully!</h3>
                        <p class="lead text-muted mb-0">Thank you for helping us keep our campus in top condition.</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> {{ session('success') }}
                        </div>
                    @else
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> Your issue has been reported successfully and is now in our system.
                        </div>
                    @endif

                    <div class="tracking-box">
                        <h5 class="mb-3 fw-bold" style="color:#212529;">Issue Details</h5>
                        <div class="issue-details text-start mx-auto" style="max-width:400px;">
                            <div class="info-item mb-3">
                                <span class="info-label"><i class="fas fa-user-cog me-2 text-primary"></i>Assigned Technician:</span>
                                <span class="info-value ms-2">
                                    @if(session('assigned_technician'))
                                        <span class="fw-semibold text-success">{{ session('assigned_technician') }}</span>
                                    @else
                                        <span class="fw-semibold text-warning">Awaiting technician assignment</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item mb-3">
                                <span class="info-label"><i class="fas fa-calendar-alt me-2 text-primary"></i>Date Reported:</span>
                                <span class="info-value ms-2">
                                    <span class="fw-semibold">{{ session('reported_at') ?? now()->format('M d, Y g:i A') }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="button-container mt-4">
                            <a href="{{ route('Student.issue_details', ['id' => session('issue_id')]) }}"
                               class="btn btn-primary">
                                <i class="fas fa-search me-2"></i> View Issue Details
                            </a>
                            <a href="{{ route('Student.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i> Return to dashboard
                            </a>
                        </div>
                    </div>

                    <div class="text-center mt-4 text-muted">
                        <p class="mb-1 fs-6">You will receive updates as your issue progresses.</p>
                        <p class="small">If you have any questions, please contact the maintenance office.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


