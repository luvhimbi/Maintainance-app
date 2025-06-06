@extends('errors::minimal')

@section('title', '429 - Too Many Requests')
@section('code', '429')

@section('message')
    🕒 Too Many Requests<br>
    You’ve made too many requests in a short period. Please wait a moment and try again.
@endsection

@section('action')
    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3 me-2">Go Back</a>
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">Return to Homepage</a>
@endsection
