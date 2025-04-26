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
    <div class="alert alert-info">
        No notifications found!
    </div>
@endforelse


</div>
@endsection