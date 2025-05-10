@extends('layouts.StudentNavbar')

@section('title', 'Profile')

@section('content')
    <div class="container py-4">
        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="h4 fw-bold mb-0">
                                @if(auth()->user()->user_role === 'Student')
                                    Student Profile
                                @else
                                    Staff Profile
                                @endif
                            </h2>
                            <a href="{{ route('test.profile.edit') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Personal Info -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h3 class="h5 fw-bold border-bottom pb-2 mb-3">Personal Information</h3>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-light-primary me-3">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold">Name</p>
                                            <p class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-light-primary me-3">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold">Email</p>
                                            <p class="mb-0">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-light-primary me-3">
                                            <i class="fas fa-phone text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold">Phone</p>
                                            <p class="mb-0">{{ $user->phone_number ?? 'Not provided' }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-light-primary me-3">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold">Address</p>
                                            <p class="mb-0">{{ $user->address ?? 'Not provided' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic/Professional Info -->
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h3 class="h5 fw-bold border-bottom pb-2 mb-3">
                                        @if(auth()->user()->user_role === 'Student')
                                            Academic Information
                                        @else
                                            Professional Information
                                        @endif
                                    </h3>
                                    
                                    @if(auth()->user()->user_role === 'Student')
                                        <!-- Student Specific Fields -->
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-light-info me-3">
                                                <i class="fas fa-id-card text-info"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Student Number</p>
                                                <p class="mb-0">{{ $roleData->student_number ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-light-info me-3">
                                                <i class="fas fa-graduation-cap text-info"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Course</p>
                                                <p class="mb-0">{{ $roleData->course ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-light-info me-3">
                                                <i class="fas fa-building text-info"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Faculty</p>
                                                <p class="mb-0">{{ $roleData->faculty ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Staff Specific Fields -->
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-light-info me-3">
                                                <i class="fas fa-id-card text-info"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Department</p>
                                                <p class="mb-0">{{ $roleData->department ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-light-info me-3">
                                                <i class="fas fa-briefcase text-info"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold">Position Title</p>
                                                <p class="mb-0">{{ $roleData->position_title ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Update Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h3 class="h5 fw-bold mb-0">Change Password</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any()))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('profile.updatePassword') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    <span class="input-group-text toggle-password" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    <span class="input-group-text toggle-password" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                                    <span class="input-group-text toggle-password" data-target="new_password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-key me-1"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.08);
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-light-primary {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        .bg-light-info {
            background-color: rgba(var(--bs-info-rgb), 0.1);
        }

        .toggle-password {
            cursor: pointer;
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(function(element) {
                element.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
        });
    </script>
@endsection