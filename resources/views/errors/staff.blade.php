@extends('errors.general')

@section('title', 'System Error')
@section('message', 'The application encountered an unexpected error. Our team has been notified.')

@auth('staff')
    @section('dashboardRoute', route('technician.dashboard'))
@endauth
