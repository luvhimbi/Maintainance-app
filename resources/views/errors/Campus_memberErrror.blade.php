@extends('errors.general')

@section('title', 'Application Error')
@section('message', 'We encountered a problem loading your content. Please try again.')

@auth('student')
    @section('dashboardRoute', route('Student.dashboard'))
@endauth
