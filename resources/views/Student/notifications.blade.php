<!-- Display all notifications -->
@extends('Layouts.StudentNavbar')
@section('title', 'Notifications')
@section('content')

<div class="container">
    <h2>Your Notifications</h2>

    <!-- Mark All as Read Button (Optional) -->
    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="mb-4">
        @csrf
        <button type="submit" class="btn btn-primary">
            Mark All as Read
        </button>
    </form>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @forelse($notifications as $notification)
    <div class="card mb-3 @if($notification->unread()) border-primary @endif">
        <div class="card-body">
            <a href="{{ route('notifications.show', $notification->id) }}" class="text-decoration-none text-dark">
                <p class="card-text">
                    {{ Str::limit($notification->data['message'], 100) }}
                    @if(strlen($notification->data['message']) > 100)
                        <span class="text-primary">...Read more</span>
                    @endif
                </p>
                <small class="text-muted">
                    {{ $notification->created_at->diffForHumans() }}
                    @if($notification->unread())
                        <span class="badge bg-primary ms-2">New</span>
                    @endif
                </small>
            </a>
        </div>
    </div>
    @empty
        <div class="empty-notification-state text-center py-5">
            <div class="empty-icon mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#6c757d" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                </svg>
            </div>
            <h5 class="text-muted">No notifications yet</h5>
            <p class="text-muted small">When you get notifications, they'll appear here</p>
        </div>
    @endforelse


</div>

    @push('styles')
        <style>
        .empty-notification-state {
        background-color: #f8f9fa;
        border-radius: 8px;
        margin: 20px 0;
        transition: all 0.3s ease;
        }

        .empty-notification-state:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        }

        .empty-icon svg {
        opacity: 0.7;
        }
        </style>
    @endpush
@endsection
