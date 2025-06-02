<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Issue</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ======================
    VARIABLES & BASE STYLES
    ====================== */
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

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* ======================
           LAYOUT CONTAINERS
           ====================== */
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
        }

        /* ======================
           PROGRESS STEPS
           ====================== */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
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
            background-color: var(--border-color);
            z-index: 0;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid var(--border-color);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .step-label {
            font-size: 0.85rem;
            color: var(--text-light);
            text-align: center;
            transition: all 0.3s ease;
        }

        /* Active & Completed Steps */
        .step.active .step-number,
        .step.completed .step-number {
            color: white;
        }

        .step.active .step-number {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
       .btn-primary{
           background-color: var(--primary-color);
           color: white;
           font-weight: 600;
           padding: 1rem;
       }
       .btn-primary:hover{
           background-color: var(--primary-hover);
           color: white;
       }
        .step.completed .step-number {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .step.completed .step-label {
            color: var(--success-color);
            font-weight: 600;
        }

        /* ======================
           ISSUE INFO SECTIONS
           ====================== */
        .issue-info {
            background-color: white;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .issue-info:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
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

        /* ======================
           INFO ITEMS & DATA
           ====================== */
        .info-item {
            margin-bottom: 1rem;
        }

        .info-item strong {
            display: block;
            margin-bottom: 0.25rem;
            color: var(--text-medium);
        }

        .info-label {
            font-weight: 600;
            color: var(--text-medium);
            min-width: 120px;
        }

        .info-value {
            color: var(--text-dark);
        }

        /* ======================
           ATTACHMENTS
           ====================== */
        .attachment-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: var(--bg-light);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .attachment-item:hover {
            background-color: white;
        }

        .attachment-item i {
            color: var(--primary-color);
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        /* Grid view for attachments */
        .attachment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .attachment-grid-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .attachment-grid-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        /* ======================
           BUTTONS
           ====================== */
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-lg {
            padding: 0.875rem 1.75rem;
            font-size: 1.05rem;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-outline-secondary {
            color: var(--text-light);
            border: 1px solid var(--border-color);
            background-color: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: var(--bg-light);
            color: var(--text-medium);
        }

        /* ======================
           UTILITY CLASSES
           ====================== */
        .badge {
            font-size: 0.9em;
            padding: 0.35em 0.65em;
            border-radius: 4px;
            font-weight: 600;
        }

        .bg-danger {
            background-color: var(--danger-color);
        }

        .bg-warning {
            background-color: var(--warning-color);
            color: var(--text-dark);
        }

        .bg-success {
            background-color: var(--success-color);
        }

        .text-muted {
            color: var(--text-light);
        }

        /* ======================
           RESPONSIVE ADJUSTMENTS
           ====================== */
        @media (max-width: 576px) {
            .info-item {
                flex-direction: column;
            }

            .info-label {
                margin-bottom: 0.25rem;
                min-width: auto;
            }

            .progress-steps {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .step:not(:last-child):after {
                display: none;
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

        <div class="container">


            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i> Issue Details</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Reporter:</strong>
                                <div>{{ $reporter->name }} (ID: {{ $formData['reporter_id'] }})</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Report Date:</strong>
                                <div>{{ now()->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-item mb-3">
                        <strong>Location:</strong>
                        <div>{{ $location->building_name }} - Floor {{ $location->floor_number }}, Room {{ $location->room_number }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="info-item">
                                <strong>Issue Type:</strong>
                                <div>{{ $formData['issue_type'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item">
                                <strong>Safety Hazard:</strong>
                                <div>{{ $formData['safety_hazard'] ? 'Yes' : 'No' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item">
                                <strong>Affected Areas:</strong>
                                <div>{{ $formData['affected_areas'] }}</div>
                            </div>
                        </div>
                    </div>

                    @if($formData['issue_type'] === 'PC')
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>PC Number:</strong>
                                    <div>{{ $formData['pc_number'] }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>PC Issue Type:</strong>
                                    <div>{{ $formData['pc_issue_type'] ?? 'Not specified' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Critical Work Affected:</strong>
                                    <div>{{ $formData['critical_work_affected'] ? 'Yes' : 'No' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="info-item mb-3">
                        <strong>Urgency Level:</strong>
                        <div>
                            @if($formData['urgency_level'] == 'High')
                                <span class="badge bg-danger">High (Score: {{ $formData['urgency_score'] ?? 'N/A' }})</span>
                            @elseif($formData['urgency_level'] == 'Medium')
                                <span class="badge bg-warning text-dark">Medium (Score: {{ $formData['urgency_score'] ?? 'N/A' }})</span>
                            @elseif($formData['urgency_level'] == 'Low')
                                <span class="badge bg-success">Low (Score: {{ $formData['urgency_score'] ?? 'N/A' }})</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <strong>Description:</strong>
                        <div class="border p-2 rounded bg-light">{{ $formData['issue_description'] }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-paperclip me-2"></i> Attachments</h5>

                    @if (count($attachments) > 0)
                        <div class="row">
                            @foreach ($attachments as $attachment)
                                <div class="col-md-4 mb-3">
                                    <div class="attachment-item p-3 border rounded">
                                        @php
                                            $icon = match(pathinfo($attachment['original_name'], PATHINFO_EXTENSION)) {
                                                'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image',
                                                'pdf' => 'fa-file-pdf',
                                                'doc', 'docx' => 'fa-file-word',
                                                default => 'fa-file'
                                            };
                                        @endphp
                                        <i class="fas {{ $icon }} fa-2x text-primary mb-2"></i>
                                        <div class="text-truncate"><small>{{ $attachment['original_name'] }}</small></div>
                                        <div class="text-muted"><small>{{ number_format($attachment['size'] / 1024, 2) }} KB</small></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">No attachments uploaded.</div>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <form action="{{ route('issue.save') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check-circle me-2"></i> Confirm and Submit
                    </button>
                </form>
                <a href="{{ route('Student.createissue') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-edit me-2"></i> Go Back and Edit
                </a>
            </div>
        </div>
@endsection


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show error message if exists
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#4361ee'
            });
        @endif

        // Form submission confirmation
        document.getElementById('confirmForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirm Submission',
                text: 'Are you sure you want to submit this issue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4361ee',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'No, go back'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
</body>
</html>
