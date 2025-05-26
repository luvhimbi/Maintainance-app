@extends('Layouts.TechnicianNavbar')
@section('title', 'Notifications')
@section('content')

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('technician.dashboard') }}" class="text-decoration-none">
                    Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Notifications
            </li>
        </ol>
    </nav>
    <h1 class="h2 fw-bold mb-0">Notifications</h1>


    <!-- Mark All as Read Button -->
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

    <div id="notifications-container">
        @forelse($notifications as $notification)
        <div class="card mb-3 @if($notification->unread()) border-primary @endif" id="notification-{{ $notification->id }}">
            <div class="card-body">
                <a href="{{ route('notifications.Techshow', $notification->id) }}" class="text-decoration-none text-dark">
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


</div>

@section('scripts')
<!-- Include Pusher JS -->
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<!-- Include SweetAlert for nice notifications (optional) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Get the authenticated user's ID
    const userId = "{{ auth()->id() }}";

    // Initialize Pusher
    const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
        cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
        encrypted: true,
        forceTLS: true
    });

    // Subscribe to the private notification channel
    const channel = pusher.subscribe('private-App.Models.User.' + userId);

    // Listen for new notifications
    channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
        // Play notification sound
        playNotificationSound();

        // Show toast notification
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: data.message,
            showConfirmButton: false,
            timer: 5000
        });

        // Prepend the new notification to the container
        const notificationsContainer = document.getElementById('notifications-container');

        // Create new notification element
        const newNotification = document.createElement('div');
        newNotification.className = 'card mb-3 border-primary';
        newNotification.id = 'notification-' + data.id;
        newNotification.innerHTML = `
            <div class="card-body">
                <a href="/technician/notifications/${data.id}" class="text-decoration-none text-dark">
                    <p class="card-text">
                        ${data.message.length > 100 ? data.message.substring(0, 100) + '...<span class="text-primary">Read more</span>' : data.message}
                    </p>
                    <small class="text-muted">
                        Just now
                        <span class="badge bg-primary ms-2">New</span>
                    </small>
                </a>
            </div>
        `;

        // Insert at the top of the container
        if (document.querySelector('.alert-info')) {
            // Remove the "no notifications" message if it exists
            document.querySelector('.alert-info').remove();
        }

        notificationsContainer.insertBefore(newNotification, notificationsContainer.firstChild);

        // Update notification count in navbar (if you have one)
        updateNotificationCount();
    });

    // Function to play notification sound
    function playNotificationSound() {
        const audio = new Audio('/path/to/notification-sound.mp3');
        audio.play().catch(e => console.log('Audio play failed:', e));
    }

    // Function to update notification count in navbar
    function updateNotificationCount() {
        const counterElement = document.getElementById('notification-counter');
        if (counterElement) {
            // You might want to make an AJAX call to get the actual count
            const currentCount = parseInt(counterElement.textContent) || 0;
            counterElement.textContent = currentCount + 1;
            counterElement.style.display = 'inline-block';
        }
    }

    // Function to mark a notification as read when clicked
    document.querySelectorAll('.card[class*="border-primary"]').forEach(card => {
        card.addEventListener('click', function() {
            const notificationId = this.id.replace('notification-', '');
            // You can add an AJAX call here to mark as read if needed
            this.classList.remove('border-primary');
            this.querySelector('.badge').remove();
        });
    });
</script>
@endsection
@endsection
