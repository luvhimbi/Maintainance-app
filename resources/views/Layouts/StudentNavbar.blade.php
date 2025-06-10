<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Maintenance System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('./css/global.css') }}">


    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .user-avatar {
    width: 36px;  /* Increased from 32px */
    height: 36px; /* Increased from 32px */
    border-radius: 50%;
    background-color: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px; /* Adjusted font size */
    color: #4b5563;
    margin-right: 0.5rem;
}

        .navbar {
            padding: 1rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
            color: #2563eb;
        }

        .nav-link {
            font-weight: 500;
            color: #4b5563;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .nav-link:hover {
            color: #2563eb;
            background-color: #f0f5ff;
        }

        .nav-link.active {
            color: #2563eb;
            background-color: #f0f5ff;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -25%);
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #4b5563;
            margin-right: 0.5rem;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f0f5ff;
            color: #2563eb;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fff5f5;
            color: #dc2626;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: white;
                padding: 1rem;
                border-radius: 0.5rem;
                margin-top: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0,0,0,.1);
            }

            .nav-link {
                padding: 0.75rem 1rem;
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('Student.dashboard') }}">
                <img src="{{ asset('images/images.png') }}" alt="Company Logo" class="me-2" style="height: 30px; width: auto;">
                <i class="fas fa-tools me-2"></i>
            OCM
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('Student.dashboard') ? 'active' : '' }}" href="{{ route('Student.dashboard') }}">
                            <i class="fas fa-home me-2"></i>Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('Student.createissue') ? 'active' : '' }}"
                           href="{{ route('Student.createissue') }}">
                            <i class="fas fa-plus-circle me-2"></i>New Issue
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('Student.view_issues') ? 'active' : '' }}"
                           href="{{ route('Student.view_issues') }}">
                            <i class="fas fa-tasks me-2"></i>Issue History
                        </a>
                    </li>

                    <li class="nav-item position-relative">
                        <a class="nav-link {{ Request::routeIs('notifications.index') ? 'active' : '' }}"
                           href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell me-2"></i>Notifications
                            @auth
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="badge bg-danger notification-badge">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            @endauth
                        </a>
                    </li>

                    <!-- User Menu -->
                @auth
    <li class="nav-item dropdown ms-lg-3">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
           id="userDropdown" role="button" data-bs-toggle="dropdown">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
            </div>
            <span class="d-none d-lg-block">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('profile') }}">
                    <i class="fas fa-user me-2"></i>Profile
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </li>
        </ul>
    </li>

                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));

            // Active link handling
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Navbar shadow on scroll
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 0) {
                    navbar.style.boxShadow = '0 2px 4px rgba(0,0,0,.08)';
                } else {
                    navbar.style.boxShadow = 'none';
                }
            });
        });
    </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Add this before closing </body> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle logout confirmation
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
                        // If confirmed, submit the form
                        logoutForm.submit();
                    }
                });
            });
        }
    });
</script>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
