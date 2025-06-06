@extends('errors::minimal')

@section('title', '500 - Internal Server Error')
@section('code', '500')

@section('message')
    😵 Something went wrong on our end.<br>
    🔧 Please try again later or contact support if the issue persists.
@endsection

@section('action')
    <div class="mt-3">
        <a href="{{ url('/') }}" class="btn btn-primary">Return to Homepage</a>
        <button onclick="location.reload()" class="btn btn-secondary ml-2">Refresh Page</button>
    </div>
@endsection
