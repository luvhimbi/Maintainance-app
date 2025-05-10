@extends('layouts.StudentNavbar')

@section('title', ' Dashboard')

@section('content')
    <div class="container py-4">
        <!-- Header Section (unchanged) -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
            <div class="mb-3 mb-md-0">
              <h1 class="fw-bold mb-2 h3">Hi , {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h1>
                <p class="lead text-muted mb-0">Track and manage your reported maintenance issues</p>
            </div>
            <a href="{{ route('Student.createissue') }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                <i class="fas fa-plus me-2"></i> Report An Issue
            </a>
        </div>

        <!-- Enhanced Issues Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h3 class="mb-0 fw-bold">Your Active Reported Issues</h3>
            </div>
            <div class="card-body p-0">
                @forelse ($issues as $issue)
                    <div class="border-bottom p-4 issue-item hover-bg-light transition position-relative">
                        <div class="row align-items-center">
                            <!-- Left Column - Issue Identity -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @php
                                            $icon = match($issue->issue_type) {
                                                'Plumbing' => 'fa-faucet-drip',
                                                'Electrical' => 'fa-bolt-lightning',
                                                'Furniture' => 'fa-couch',
                                                'HVAC' => 'fa-fan',
                                                'Internet' => 'fa-network-wired',
                                                'Cleaning' => 'fa-broom',
                                                default => 'fa-circle-exclamation'
                                            };
                                            $urgencyColor = match($issue->urgency_level) {
                                                'High' => 'danger',
                                                'Medium' => 'warning',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $urgencyColor }} rounded-circle p-3 shadow-sm">
                                            <i class="fas {{ $icon }} fa-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-semibold">{{ $issue->issue_type }} Issue</h5>
                                        <div class="text-muted">
                                            <span class="d-inline-block me-3">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $issue->location->building_name ?? 'N/A'}}, Room {{ $issue->location->room_number ?? 'N/A'}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Middle Column - Timeline & Status -->
                            <div class="col-md-3">
                                <div>
                                    <span class="badge bg-{{ $urgencyColor }} text-white px-3 py-1 rounded-pill">
                                        {{ $issue->urgency_level }} Priority
                                    </span>
                                </div>
                            </div>

                            <!-- Right Column - Actions -->
                            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                                    @if($issue->issue_status === 'Open')
                                        <a href="{{ route('Student.editissue', $issue->issue_id) }}"
                                           class="btn btn-outline-secondary px-3 py-2 btn-action">
                                            <i class="fas fa-pencil-alt me-1"></i> Edit
                                        </a>
                                    @endif

                                    <a href="{{ route('Student.issue_details', $issue->issue_id) }}"
                                       class="btn btn-outline-primary px-4 py-2 btn-action">
                                        Details <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Status Indicator -->
                        <div class="position-absolute top-0 end-0 mt-3 me-3">
                            <span class="badge
                                @if($issue->issue_status == 'Open') bg-primary
                                @elseif($issue->issue_status == 'In Progress') bg-warning text-dark
                                @elseif($issue->issue_status == 'Resolved') bg-success
                                @endif px-3 py-1 rounded-pill">
                                <i class="fas fa-circle me-1 small-status"></i>
                                {{ $issue->issue_status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
                        <h4 class="fw-semibold">No active issues to display</h4>
                        <p class="text-muted">Start by reporting a new maintenance issue</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination (unchanged) -->
        @if($issues->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $issues->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .hover-bg-light:hover {
                background-color: #f8f9fa;
                transform: translateY(-2px);
            }
            .transition {
                transition: all 0.2s ease;
            }
            .small-status {
                font-size: 0.6em;
                vertical-align: middle;
            }
            .btn-action {
                transition: all 0.2s ease;
            }
            .btn-action:hover {
                transform: translateY(-1px);
            }
            .issue-item {
                cursor: pointer;
                border-left: 3px solid transparent;
            }
            .issue-item:hover {
                border-left-color: var(--bs-primary);
            }
        </style>
    @endpush
@endsection
