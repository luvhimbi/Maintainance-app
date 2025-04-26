<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
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
    <div class="container mt-5">
          <div class="brand-logo">
                <img src="{{ asset('images/images.png') }}" alt="Company Logo" class="img-fluid">
            </div>
             <h1 class="form-title text-center">Change Password</h1>
        @if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('password.update', ['token' => $token]) }}" method="POST">
    @csrf
    
    <div class="form-group mb-3">
        <label for="email">Email Address</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" 
               id="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group mb-3">
        <label for="password">New Password</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               id="password" name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="form-group mb-4">
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" class="form-control" 
               id="password_confirmation" name="password_confirmation" required>
    </div>
    
    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
</form>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>