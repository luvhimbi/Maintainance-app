<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Font Awesome (for role icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
        }

        .login-container {
            max-width: 700px;
            margin: 60px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .form-title {
            font-weight: 600;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        .input-group-icon {
            padding: 10px 15px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-right: none;
        }

        .form-control-with-icon {
            border-left: none;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }

        .btn-secondary {
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }

        .role-selection {
            margin-top: 10px;
        }

        .form-check {
            display: inline-block;
            margin-right: 15px;
        }

        .form-check-input {
            margin-right: 5px;
        }

        .password-toggle {
            cursor: pointer;
            padding: 10px 15px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-left: none;
        }

        footer {
            background-color: #f1f1f1;
            padding: 20px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Navbar -->
@include('Layouts.GuestNavBar')

<!-- Login Form -->
<div class="container">
    <div class="login-container">
        <h1 class="form-title text-center">Login</h1>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="form-label">Email address</label>
                <div class="input-group">
                    <span class="input-group-icon"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control form-control-with-icon" id="email" name="email" required>
                </div>
                <div id="emailHelp" class="form-text">Enter your registered email address</div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-icon"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control form-control-with-icon" id="password" name="password" required>
                    <span class="password-toggle" id="togglePassword"><i class="bi bi-eye"></i></span>
                </div>
            </div>

            <!-- Role Selection -->
            <div class="mb-4">
                <label class="form-label fw-bold">Select User Role</label>
                <div class="role-selection">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="role" id="role-student" value="Student" checked required>
                        <label class="form-check-label" for="role-student">
                            <i class="fas fa-user-graduate me-2"></i> Student
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="role" id="role-staff" value="Staff_Member">
                        <label class="form-check-label" for="role-staff">
                            <i class="fas fa-briefcase me-2"></i> Staff
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="role" id="role-technician" value="Technician">
                        <label class="form-check-label" for="role-technician">
                            <i class="fas fa-tools me-2"></i> Technician
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="role" id="role-admin" value="Admin">
                        <label class="form-check-label" for="role-admin">
                            <i class="fas fa-user-shield me-2"></i> Admin
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
            </div>

            <!-- Forgot Password Link -->
            <p class="text-center mt-3">Forgot your password? <a href="{{ route('password.reset') }}">Reset Password</a></p>
        </form>
    </div>
</div>

<!-- Footer -->
@include('Layouts.footer')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });

        @if(session('password_changed'))
        Swal.fire({
            title: 'Password Updated',
            text: 'Your password has been updated successfully. Please login with your new password.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0d6efd'
        });
        @endif
    });
</script>
</body>
</html>
