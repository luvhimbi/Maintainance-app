@extends('Layouts.StudentNavbar')
@section('title', 'Notifications')
@section('content')

    <div class="container py-4"> {{-- Added py-4 for top/bottom spacing --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1 text-dark">Your Notifications</h1>
                <p class="text-muted mb-0">Stay informed about your reported issues and system updates.</p>
            </div>
            <form action="{{ route('notifications.markAllRead') }}" method="POST" class="mt-3 mt-md-0">
                @csrf
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-check-double me-2"></i> Mark All as Read
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @forelse($notifications as $notification)
            <div class="card mb-3 rounded-4 shadow-sm notification-card {{ $notification->unread() ? 'border-primary border-2' : 'border-light' }}"> {{-- Added rounded-4 and shadow-sm, border for unread --}}
                <div class="card-body p-4"> {{-- Added padding for card body --}}
                    <a href="{{ route('notifications.show', $notification->id) }}" class="text-decoration-none text-dark d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary-subtle text-primary p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-bell fa-lg"></i> {{-- Bell icon for notifications --}}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-text fw-medium mb-1">
                                {{ Str::limit($notification->data['message'], 150) }} {{-- Increased limit slightly --}}
                                @if(strlen($notification->data['message']) > 150)
                                    <span class="text-primary fw-semibold">...Read more</span>
                                @endif
                            </p>
                            <small class="text-muted d-flex align-items-center">
                                <i class="fas fa-clock me-1"></i> {{-- Clock icon for timestamp --}}
                                {{ $notification->created_at->diffForHumans() }}
                                @if($notification->unread())
                                    <span class="badge bg-primary ms-2 py-1 px-2 rounded-pill fw-medium">New</span> {{-- Styled New badge --}}
                                @endif
                            </small>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div class="card border-0 bg-light rounded-4 shadow-sm"> {{-- Styled empty state card --}}
                <div class="card-body py-5 text-center px-3">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-bell-slash fa-4x text-muted"></i> {{-- Updated icon for no notifications --}}
                    </div>
                    <h4 class="fw-bold mb-2 text-dark">No notifications yet</h4>
                    <p class="text-muted mb-0">When you get notifications, they'll appear here.</p>
                </div>
            </div>
        @endforelse
    </div>

    @push('styles')
        <style>
            /* General body and card styling */
            body {
                background-color: #f8f9fa; /* Light grey background */
                font-family: 'Inter', sans-serif; /* Consistent font */
                min-height: 100vh; /* Ensure body takes full viewport height */
                display: flex; /* Enable flexbox */
                flex-direction: column; /* Arrange content in a column */
            }

            .card {
                border: 1px solid #e0e0e0; /* Subtle border for cards */
                box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05); /* Lighter shadow */
            }

            .card-header {
                background-color: #ffffff; /* White background for headers */
                color: #343a40; /* Dark text */
                border-bottom: 1px solid #e9ecef; /* Light border at the bottom */
            }

            .card-header h2, .card-header p {
                color: #343a40 !important; /* Ensure text is dark */
            }

            /* Custom subtle badge colors */
            .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
            .text-primary { color: #0d6efd !important; }

            .bg-success-subtle { background-color: rgba(40, 167, 69, 0.1) !important; }
            .text-success { color: #28a745 !important; }

            .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
            .text-warning { color: #ffc107 !important; }

            .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
            .text-danger { color: #dc3545 !important; }

            .bg-info-subtle { background-color: rgba(23, 162, 184, 0.1) !important; }
            .text-info { color: #17a2b8 !important; }

            .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
            .text-secondary { color: #6c757d !important; }

            /* Buttons */
            .btn-primary {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
            .btn-primary:hover {
                background-color: #0b5ed7;
                border-color: #0a58ca;
            }

            .btn-outline-secondary {
                color: #6c757d;
                border-color: #6c757d;
                background-color: #ffffff;
                transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
            }
            .btn-outline-secondary:hover {
                background-color: #6c757d;
                color: white;
                border-color: #6c757d;
            }

            /* Notification card specific styles */
            .notification-card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .notification-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,.08);
            }
            .notification-card a {
                display: flex; /* Ensure the flex layout applies to the whole link */
                align-items: center;
                width: 100%;
                height: 100%;
            }

            /* Empty state styling */
            .empty-state-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(108, 117, 125, 0.1); /* Secondary subtle for empty */
                border-radius: 50%;
            }
            .empty-state-icon i {
                color: #6c757d; /* Muted color for icon */
            }
        </style>
    @endpush

    @push('scripts')
        {{-- No specific JS needed for this page beyond Bootstrap's built-in alert dismiss --}}
    @endpush
@endsection
