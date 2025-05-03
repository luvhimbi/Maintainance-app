<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('./css/global.css') }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Inline Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            font-size: 0.9rem;
            color: #495057;
        }
        .navbar {
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .navbar-brand {
            font-weight: 600;
            color: #3a7bd5;
        }
        .nav-link {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            color: #495057;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link:focus {
            color: #3a7bd5;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            padding: 0.5rem 0;
            font-size: 0.85rem;
        }
        .dropdown-item {
            padding: 0.5rem 1.25rem;
            transition: all 0.2s ease;
        }
        .dropdown-item:hover {
            background-color: #f1f7fe;
            color: #3a7bd5;
        }
        .badge {
            font-size: 0.65rem;
            font-weight: 500;
            padding: 0.25em 0.5em;
        }
        .container {
            max-width: 1400px;
        }
        .navbar-nav .nav-item {
            margin: 0 0.25rem;
        }
        .dropdown-toggle::after {
            margin-left: 0.3em;
            vertical-align: 0.15em;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top bg-white">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/images.png') }}" alt="Company Logo" class="me-2" style="height: 30px; width: auto;">
                <i class="fas fa-tools me-2"></i> <span class="fw-bold" style="color: #3a7bd5;">OCM</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <!-- Dashboard Link -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <!-- Maintenance Tasks Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="tasksDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-tools me-1"></i> Maintenance
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="tasksDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.tasks.view') }}">
                                <i class="fas fa-tasks me-2"></i> Manage Tasks
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.locations.index') }}">
                                <i class="fa-solid fa-location-dot me-2"></i> Locations
                            </a></li>
                        </ul>
                    </li>
                    <!-- User Management Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users-cog me-1"></i> Users
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="usersDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.students.index') }}">
                                <i class="fas fa-user-graduate me-2"></i> Personnel (students, staff)
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.technicians.index') }}">
                                <i class="fas fa-user-tie me-2"></i> Maintenance Staff
                            </a></li>
                        </ul>
                    </li>
                    <!-- Reports -->
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar me-2"></i> Reports
                        </a>
                    </li>
                    <!-- Notifications -->
                    <li class="nav-item position-relative">
                        <a class="nav-link">
                            <i class="fas fa-bell me-2"></i> Notifications
                            @auth
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            @endauth
                        </a>
                    </li>
                    <!-- User Dropdown -->
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="me-2 d-none d-lg-inline">
                                    <span>{{ Auth::user()->first_name }}</span>
                                </div>
                                <i class="fas fa-user-circle" style="font-size: 1.25rem;"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('adminProfile') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        @yield('content')
    </div>

    @stack('scripts')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Logout Confirmation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForm = document.querySelector('form[action="{{ route('logout') }}"]');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You will be logged out of the system!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, logout!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            logoutForm.submit();
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
