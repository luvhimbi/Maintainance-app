@extends('errors.layout')

@section('code', 'Error')

@section('content')
    <div class="error-icon mb-4">
        <i class="fas fa-exclamation-triangle fa-5x text-danger"></i>
    </div>
    <h1 class="error-code mb-3">Oops!</h1>
    <h2 class="h4 mb-3">@yield('title', 'Something went wrong')</h2>
    <p class="lead mb-4">@yield('message', 'We encountered an error while processing your request.')</p>

    <div class="error-actions">
        <button onclick="window.location.reload()" class="btn btn-primary">
            <i class="fas fa-sync-alt me-2"></i> Retry
        </button>

        @auth
            <a href="@yield('dashboardRoute')" class="btn btn-outline-secondary">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        @else
            <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-2"></i> Home
            </a>
        @endauth

        <button onclick="window.history.back()" class="btn btn-outline-dark">
            <i class="fas fa-arrow-left me-2"></i> Go Back
        </button>
    </div>

    @if(config('app.debug'))
        <div class="mt-4 text-start">
            <details>
                <summary class="text-muted">Error Details</summary>
                <div class="alert alert-danger mt-2">
                    <strong>Message:</strong> {{ $exception->getMessage() }}<br>
                    <strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}
                </div>
            </details>
        </div>
    @endif
@endsection
