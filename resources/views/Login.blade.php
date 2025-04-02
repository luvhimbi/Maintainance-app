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
        .login-container {
            max-width: 650px;
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
        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand-logo img {
            height: 60px;
            width: auto;
        }
        .btn-secondary {
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
        .action-links {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            
            
            <div class="brand-logo">
                <img src="{{ asset('images/images.png') }}" alt="Company Logo" class="img-fluid">
            </div>
            <h1 class="form-title text-center"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h1>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-icon"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control form-control-with-icon" id="email" name="email" required>
                    </div>
                    <div id="emailHelp" class="form-text">Enter your registered email address</div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-icon"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control form-control-with-icon" id="password" name="password" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="role" class="form-label">Select Role</label>
                    <div class="input-group">
                        <span class="input-group-icon"><i class="bi bi-person-badge"></i></span>
                        <select class="form-select form-control-with-icon" id="role" name="role" required>
                            <option value="Student">Student</option>
                            <option value="Technician">Technician</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
                    <a href="{{ route('password.reset') }}" class="btn btn-secondary"><i class="bi bi-key me-2"></i>Reset Password</a>
                </div>
                
                
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>