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

    <!-- Poppins Font -->
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

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            flex: 1;
        }

        .auth-container {
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

        .btn-primary {
            width: 100%;
            padding: 10px;
            font-weight: 500;
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

<div class="page-wrapper">

    @include('Layouts.GuestNavBar')

    <div class="content-wrapper">
        <div class="container">
            <div class="auth-container">

                <h1 class="form-title text-center">Reset Password</h1>

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" id="resetPasswordForm">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="emailHelp" class="form-text">Enter your email to receive a password reset link</div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Send Password Reset Link
                        </button>
                    </div>

                    <div class="auth-links">
                        Remember your password? <a href="{{ route('login') }}">Login here</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @include('Layouts.footer')

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Sending Email...',
                html: '<div class="spinner-border text-primary" role="status"></div><br>Sending password reset link...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const email = form.querySelector('input[name="email"]').value;
            fetch("{{ route('password.email') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
            .then(async response => {
                let data = null;
                try {
                    data = await response.json();
                } catch (err) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                setTimeout(() => {
                    Swal.close();
                    if (response.ok && data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.status,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: (data.errors && data.errors.email) ? data.errors.email[0] : 'Validation failed.',
                            confirmButtonText: 'OK'
                        });
                    }
                }, 800);
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonText: 'OK'
                });
            });
        });
    }
});
</script>
</body>
</html>
