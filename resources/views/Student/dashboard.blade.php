@extends('layouts.StudentNavbar')

@section('title', 'Student Dashboard')

@section('content')
    <div class="container py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
            <div class="mb-3 mb-md-0">
                <h1 class="fw-bold mb-2 h3">Welcome back, {{ Auth::user()->username }}</h1>
                <p class="lead text-muted mb-0">Track and manage your reported maintenance issues</p>
            </div>
            <a href="{{ route('Student.createissue') }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                <i class="fas fa-plus me-2"></i> Report New Issue
            </a>
        </div>
        

        <!-- Issues Section -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h3 class="mb-0 fw-bold">Your Active Reported Issues</h3>
            </div>
            <div class="card-body p-0">
                @forelse ($issues as $issue)
                    <div class="border-bottom p-4 hover-bg-light transition">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @php
                                            $icon = match($issue->issue_type) {
                                                'Plumbing' => 'fa-faucet',
                                                'Electrical' => 'fa-bolt',
                                                'Furniture' => 'fa-chair',
                                                'HVAC' => 'fa-temperature-high',
                                                'Internet' => 'fa-wifi',
                                                'Cleaning' => 'fa-broom',
                                                default => 'fa-exclamation-circle'
                                            };
                                            $color = match($issue->urgency_level) {
                                                'High' => 'danger',
                                                'Medium' => 'warning',
                                                default => 'primary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $color }} rounded-circle p-3">
                                            <i class="fas {{ $icon }} fa-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $issue->issue_type }} Issue</h5>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $issue->location->building_name ?? 'unknown building name'}}, Room {{ $issue->location->room_number ?? 
                                            'unknown room number'}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>{{ $issue->report_date}}</span>
                                </div>
                                <div class="mt-2">
                                    <span class="badge 
                                        @if($issue->issue_status == 'Open') bg-primary
                                        @elseif($issue->issue_status == 'In Progress') bg-warning text-dark
                                        @elseif($issue->issue_status == 'Resolved') bg-success
                                        @endif px-3 py-1 rounded-pill">
                                        {{ $issue->issue_status }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                
                                <a href="{{ route('Student.issue_details', $issue->issue_id) }}" 
                                   class="btn btn-outline-primary px-4 py-2">
                                    View Details <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
                        <h4>No Issues Reported Yet</h4>
                        <p class="text-muted">You haven't submitted any maintenance requests yet.</p>
                        <a href="{{ route('Student.createissue') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i> Report Your First Issue
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
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
        }
        .transition {
            transition: all 0.2s ease;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #3f80ea 0%, #1e3a8a 100%);
        }
        .bg-gradient-warning {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
    </style>
    @endpush
@endsection