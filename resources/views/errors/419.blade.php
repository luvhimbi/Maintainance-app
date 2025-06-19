@extends('errors::minimal')

@section('title', '419 - Page Expired')
@section('code', '419')

@section('message')
    ⏳ Page Expired<br>
    Your session has expired. Please refresh the page or log in again.
@endsection

@section('action')
    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3 me-2">Go Back</a>
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">Return to Homepage</a>
@endsection
