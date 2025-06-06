@extends('errors::minimal')

@section('title', '404 - Page Not Found')
@section('code', '404')

@section('message')
    Oops! Page Not Found<br>
    The page you're looking for might have been removed, renamed, or is temporarily unavailable.
@endsection

@section('action')
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Go to Homepage</a>
@endsection
