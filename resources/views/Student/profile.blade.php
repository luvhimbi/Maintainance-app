@extends('layouts.StudentNavbar')

@section('title', 'Profile')

@section('content')
    <div class="container">

        <h1>Profile</h1>
        <p><Strong>Name:  </Strong> {{ $user->first_name }} {{ $user->last_name }}</p>
        <p><strong>Email: </strong> {{ $user->email }}</p>
        <p><strong>Phone number: </strong> {{ $user->phone_number}}</p>
        <p><strong>Role:  </strong> {{ $user->user_role }}</p>
        <p><strong>address: </strong> {{ $user->address}}</p>
        <p><strong>student/staff id: </strong>{{$campus_member->student_staff_id}}</p>
        <p><strong>faculty/department: </strong>{{$campus_member->faculty_department}}</p>
        <p><strong>program/course: </strong>{{$campus_member->program_course}}</p>
        <p><strong>year of study: </strong>{{$campus_member->year_of_study}}</p>

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
        @push('styles')
        <style>
            .container {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
            }

            .profile-item {
                flex: 1 1 200px; /* This allows each item to grow and shrink with a minimum width of 200px */
                max-width: 300px; /* Optional, limits the size of each item */
                padding: 10px;
                box-sizing: border-box;
                border: 1px solid #ddd;
                border-radius: 8px;
                background-color: #f9f9f9;
            }

            /* Optional: Add responsive styling to adjust layout on smaller screens */
            @media (max-width: 768px) {
                .profile-container {
                    flex-direction: column; /* Stacks the items vertically on small screens */
                }
            }
        </style>
    @endpush
@endsection
