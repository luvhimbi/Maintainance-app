@extends('errors::minimal')

@section('title', '401 - Unauthorized')
@section('code', '401')

@section('message')
    🚫 Unauthorized Access<br>
    You must be logged in or do not have the right credentials to access this page.
@endsection

@section('action')
    <a href="{{ route('login') }}" class="btn btn-primary mt-3 me-2">Login</a>
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">Return to Homepage</a>
@endsection
