<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .auth-container {
            max-width: 700px;
            margin: 80px auto;
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
        .btn-primary {
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand-logo img {
            height: 60px;
            width: auto;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
        }
        .password-toggle {
            cursor: pointer;
            padding: 10px 15px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-left: none;
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="brand-logo">
        <img src="{{ asset('images/images.png') }}" alt="Company Logo" class="img-fluid">
    </div>
    <h1 class="form-title text-center">Reset Password</h1>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('password.update', ['token' => $token]) }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                    <span class="input-group-icon">
                        <i class="bi bi-envelope"></i>
                    </span>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
                    <span class="input-group-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password" required>
                <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="bi bi-eye"></i>
                    </span>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
                    <span class="input-group-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                <input type="password" class="form-control"
                       id="password_confirmation" name="password_confirmation" required>
                <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="bi bi-eye"></i>
                    </span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-arrow-clockwise me-2"></i>Reset Password
        </button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = passwordField.nextElementSibling.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
</body>
</html>
