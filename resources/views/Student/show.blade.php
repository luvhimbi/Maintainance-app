@extends('Layouts.StudentNavbar')
@section('title', 'Notification Details')
@section('content')

<div class="container py-4">
    <div class="card border-0 shadow">
        <!-- Header with Status -->
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bell text-primary me-2"></i>
                    <h5 class="mb-0 text-primary">Notification Details</h5>
                    @if(!$notification->read_at)
                        <span class="badge bg-primary ms-2">New</span>
                    @endif
                </div>
                <div class="d-flex">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <form id="deleteNotificationForm" action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="alert {{ $notification->read_at ? 'alert-secondary' : 'alert-primary' }} mb-0 py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-{{ $notification->read_at ? 'check-circle' : 'exclamation-circle' }} me-2"></i>
                    <div>
                        <strong class="small">{{ $notification->read_at ? 'Read' : 'Unread' }}</strong>
                        <div class="small text-muted">Received {{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="notification-content">
                <!-- Title -->
                

                <!-- Main Message -->
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-envelope me-2"></i>Message
                        </h6>
                        <div class="message-content">
                            {!! nl2br(e($notification->data['message'])) !!}
                        </div>
                    </div>
                </div>
                
                <!-- Changes Section -->
                @if(isset($notification->data['changes']) && count($notification->data['changes']))
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-exchange-alt me-2"></i>Changes
                        </h6>
                        <div class="changes-list">
                            <ul class="list-group list-group-flush">
                                @foreach($notification->data['changes'] as $change)
                                    <li class="list-group-item bg-light d-flex align-items-center py-2">
                                        <i class="fas fa-circle text-primary me-2" style="font-size: 6px;"></i>
                                        <small>{{ $change }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Additional Details -->
                @if(isset($notification->data['details']))
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Additional Details
                        </h6>
                        <div class="details-content">
                            @foreach($notification->data['details'] as $key => $value)
                                <div class="mb-2">
                                    <small><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Action Buttons -->
                @if(isset($notification->data['action_url']))
                <div class="mt-4 text-center">
                    <a href="{{ $notification->data['action_url'] }}" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i>
                        {{ $notification->data['action_text'] ?? 'View Details' }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .list-group-item {
        border: none;
        padding: 0.5rem 1rem;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .notification-content {
        max-width: 800px;
        margin: 0 auto;
    }
    .alert {
        border-radius: 8px;
        border: none;
    }
    .message-content {
        white-space: pre-line;
        line-height: 1.5;
        font-size: 0.95rem;
    }
    .changes-list {
        max-height: 250px;
        overflow-y: auto;
    }
    .details-content {
        line-height: 1.6;
    }
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }
    h6 {
        font-size: 0.95rem;
    }
    .text-primary {
        color: #0d6efd !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Delete Notification?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteNotificationForm').submit();
            }
        });
    }
</script>

@endsection