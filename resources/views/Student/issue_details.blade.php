@extends('layouts.StudentNavbar')
@section('title', 'Issue Details')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-2 mb-md-0">Issue Details</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to  dashboard?
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-light py-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-circle me-2 text-primary"></i>{{ $issue->issue_type }}
                    </h4>
                </div>
                <div class="col-12 col-md-4 mt-2 mt-md-0 text-md-end">
                    <span class="me-2"><i class="fas fa-calendar-alt me-1"></i> Reported: {{ \Carbon\Carbon::parse($issue->report_date)->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-4 mb-md-0">
                    <div class="mb-4">
                        <h5 class="text-muted fs-6">Location</h5>
                        <p class="card-text fs-5">
                            <i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                            {{ $issue->location->building_name ?? 'unknown building name'}}, Room {{ $issue->location->room_number ??'unknown room no' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-muted fs-6">Description</h5>
                        <p class="card-text">{{ $issue->issue_description }}</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Status</h5>
                            <div>
                                @if($issue->issue_status == 'Open')
                                    <span class="badge bg-primary">Open</span>
                                @elseif($issue->issue_status == 'In Progress')
                                    <span class="badge bg-warning text-dark">In Progress</span>
                                @elseif($issue->issue_status == 'Resolved')
                                    <span class="badge bg-success">Resolved</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted fs-6">Urgency</h5>
                            <div>
                                @if($issue->urgency_level == 'Low')
                                    <span class="badge bg-success">Low</span>
                                @elseif($issue->urgency_level == 'Medium')
                                    <span class="badge bg-warning text-dark">Medium</span>
                                @elseif($issue->urgency_level == 'High')
                                    <span class="badge bg-danger">High</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0 rounded-3 mb-3">
                        <div class="card-body">
                            <h5 class="card-title fs-6">
                                <i class="fas fa-user-cog me-2"></i>Assigned Technician
                            </h5>
                            @if ($issue->task && $issue->task->assignee)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; flex-shrink: 0;">
                                        {{ substr($issue->task->assignee->username, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold">{{ $issue->task->assignee->username }}</p>
                                    </div>
                                </div>
                                <div class="ms-1 ps-1 border-start">
                                    <p class="mb-1 small">
                                        <i class="fas fa-envelope me-2 text-secondary"></i>{{ $issue->task->assignee->email }}
                                    </p>
                                    <p class="mb-0 small">
                                        <i class="fas fa-phone me-2 text-secondary"></i>{{ $issue->task->assignee->phone_number }}
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-secondary py-2 mb-0">
                                    <small><i class="fas fa-info-circle me-1"></i>No technician assigned yet</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="mb-3">
                <i class="fas fa-paperclip me-2"></i>Attachments
            </h5>
            @if ($issue->attachments->count() > 0)
                <div class="row g-2">
                    @foreach ($issue->attachments as $attachment)
                        @php
                            $fileExtension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);
                            $iconClass = 'fa-file';

                            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $iconClass = 'fa-file-image';
                            } elseif (in_array($fileExtension, ['pdf'])) {
                                $iconClass = 'fa-file-pdf';
                            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                $iconClass = 'fa-file-word';
                            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                $iconClass = 'fa-file-excel';
                            }
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4">
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <i class="fas {{ $iconClass }} fa-2x me-3 text-secondary"></i>
                                        <div class="text-truncate">{{ $attachment->original_name }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary" role="alert">
                    <i class="fas fa-info-circle me-2"></i>No attachments found
                </div>
            @endif
        </div>
    </div>

    <!-- Task Updates Section -->
    <div class="mt-4">
        <h5 class="mb-3">
            <i class="fas fa-tasks me-2"></i>Task Updates
        </h5>

        @if ($issue->task && $issue->task->updates->count() > 0)
            <div class="timeline">
                @foreach ($issue->task->updates->sortByDesc('update_timestamp') as $update)
                    <div class="timeline-item mb-4">
                        <div class="timeline-badge 
                            @if($update->status_change == 'In Progress') bg-warning
                            @elseif($update->status_change == 'Resolved') bg-success
                            @else bg-primary @endif">
                            <i class="fas 
                                @if($update->status_change == 'In Progress') fa-wrench
                                @elseif($update->status_change == 'Resolved') fa-check
                                @else fa-info @endif"></i>
                        </div>
                        <div class="timeline-content card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        @if($update->staff)
                                            <div class="symbol symbol-35px symbol-circle me-2">
                                                <span class="symbol-label bg-light-primary text-primary fw-bold">
                                                    {{ substr($update->staff->username, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong>{{ $update->staff->username }}</strong>
                                                <span class="badge bg-secondary ms-2">{{ $update->staff->user_role }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">[Staff removed]</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($update->update_timestamp)->format('M j, Y g:i A') }}
                                    </small>
                                </div>
                                <div class="mb-2">
                                    <span class="badge 
                                        @if($update->status_change == 'In Progress') bg-warning text-dark
                                        @elseif($update->status_change == 'Resolved') bg-success
                                        @else bg-primary @endif">
                                        {{ $update->status_change }}
                                    </span>
                                </div>
                                <p class="mb-0">{{ $update->update_description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-secondary">
                <i class="fas fa-info-circle me-2"></i>No task updates available yet
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-badge {
        position: absolute;
        left: -20px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
    }
    .timeline-content {
        position: relative;
        margin-left: 20px;
        border-left: 3px solid #dee2e6;
        padding-left: 20px;
    }
    .timeline-item:last-child .timeline-content {
        border-left-color: transparent;
    }
    .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .symbol-35px {
        width: 35px;
        height: 35px;
    }
    .symbol-circle {
        border-radius: 50%;
    }
    .symbol-label {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        width: 100%;
        height: 100%;
    }
</style>
@endpush