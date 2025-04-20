@extends('Layouts.StudentNavbar')
@section('title', 'Notification Details')
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Notification Details</h4>
            <div>
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this notification?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="notification-detail">
                <div class="mb-4">
                    <h5>Message:</h5>
                    <p class="lead">{{ $notification->data['message'] }}</p>
                </div>
                
                @if(isset($notification->data['changes']) && count($notification->data['changes']))
                <div class="mb-4">
                    <h5>Changes:</h5>
                    <ul class="list-group">
                        @foreach($notification->data['changes'] as $change)
                            <li class="list-group-item">{{ $change }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div class="mb-4">
                    <h5>Details:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Received:</strong> {{ $notification->created_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge {{ $notification->read_at ? 'bg-secondary' : 'bg-primary' }}">
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

@endsection