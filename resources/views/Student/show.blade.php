@extends('Layouts.StudentNavbar')
@section('title', 'Notification Details')
@section('content')

<div class="container py-4">
    <div class="card border-0 shadow">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 text-primary">
                <i class="fas fa-bell me-2"></i>Notification Details
            </h4>
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
        
        <div class="card-body">
            <div class="notification-detail">
                <!-- Main Message Section -->
                <div class="mb-4 p-3 bg-light rounded">
                    <h5 class="text-muted mb-3">
                        <i class="fas fa-envelope me-2"></i>Message
                    </h5>
                    <div class="p-3 bg-white rounded border">
                        <p class="mb-0">{{ $notification->data['message'] }}</p>
                    </div>
                </div>
                
                <!-- Changes Section (if exists) -->
                @if(isset($notification->data['changes']) && count($notification->data['changes']))
                <div class="mb-4 p-3 bg-light rounded">
                    <h5 class="text-muted mb-3">
                        <i class="fas fa-exchange-alt me-2"></i>Changes
                    </h5>
                    <div class="bg-white rounded border">
                        <ul class="list-group list-group-flush">
                            @foreach($notification->data['changes'] as $change)
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                                    {{ $change }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                
                <!-- Metadata Section -->
                <div class="p-3 bg-light rounded">
                    <h5 class="text-muted mb-3">
                        <i class="fas fa-info-circle me-2"></i>Details
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="p-3 bg-white rounded border h-100">
                                <p class="mb-1 text-muted small">Received At</p>
                                <p class="mb-0">
                                    <i class="far fa-clock me-2 text-primary"></i>
                                    {{ $notification->created_at->format('M j, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-3 bg-white rounded border h-100">
                                <p class="mb-1 text-muted small">Status</p>
                                <p class="mb-0">
                                    <span class="badge rounded-pill {{ $notification->read_at ? 'bg-secondary' : 'bg-primary' }}">
                                        <i class="fas fa-{{ $notification->read_at ? 'check' : 'exclamation' }} me-1"></i>
                                        {{ $notification->read_at ? 'Read' : 'Unread' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .list-group-item {
        border-left: 0;
        border-right: 0;
    }
    .list-group-item:first-child {
        border-top: 0;
    }
    .list-group-item:last-child {
        border-bottom: 0;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>

<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteNotificationForm').submit();
            }
        });
    }
</script>

@endsection