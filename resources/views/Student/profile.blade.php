@extends('layouts.StudentNavbar')

@section('title', 'Profile')

@section('content')
    <div class="container">
        <h1>Profile</h1>
        <p><strong>Name:</strong> {{ $user->username }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone number:</strong> {{ $user->phone_number}}</p>
        <p><strong>Role:</strong> {{ $user->user_role }}</p>

        <a href="{{ route('test.profile.edit') }}" class="btn btn-primary mb-3">Edit Profile</a>
        @if (session('success'))
            <div class="alert alert-success">
            {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            </div>
        @endif
<!--todo make this work-->
        <form action="{{ route('profile.updatePassword') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
            </div>

            <!-- New Password -->
            <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
            </div>

            <!-- Confirm New Password -->
            <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Update Password</button>
        </form>
@endsection
