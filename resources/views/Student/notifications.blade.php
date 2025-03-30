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

    <!-- Notifications List -->
    @forelse($notifications as $notification)
        <div class="card mb-3 @if($notification->unread()) border-primary @endif">
            <div class="card-body">
                <p class="card-text">
                    {{ $notification->data['message'] }}
                </p>
                <small class="text-muted">
                    {{ $notification->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            No notifications found!
        </div>
    @endforelse

    <!-- Pagination -->
    {{ $notifications->links() }}
</div>
@endsection