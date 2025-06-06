@extends('errors::minimal')
@section('title', '403 - Forbidden')
@section('code', '403')

@section('message')
    {{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
@endsection

@section('action')
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Return to Homepage</a>
@endsection
