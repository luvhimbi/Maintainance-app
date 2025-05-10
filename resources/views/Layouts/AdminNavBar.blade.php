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
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar-brand img {
            height: 30px;
            width: auto;
            margin-right: 0.5rem;
        }

        .sidebar-brand span {
            font-weight: 600;
            color: #3a7bd5;
            font-size: 1.1rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-item {
            margin: 0.25rem 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #495057;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: #f1f7fe;
            color: #3a7bd5;
        }

        .sidebar-link i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .sidebar-dropdown {
            list-style: none;
            padding-left: 3.5rem;
            margin: 0;
            display: none;
        }

        .sidebar-dropdown.show {
            display: block;
        }

        .sidebar-dropdown .sidebar-link {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Bar Styles */
        .topbar {
            background: #fff;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-badge {
            position: relative;
            display: inline-block;
        }

        .notification-badge a {
            color: #495057;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .notification-badge a:hover {
            background: #f1f7fe;
            color: #3a7bd5;
        }

        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 0.25em 0.5em;
            font-size: 0.65rem;
            border-radius: 10px;
            border: 2px solid #fff;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .user-dropdown:hover {
            background: #f1f7fe;
        }

        .user-dropdown img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-dropdown .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-dropdown .user-name {
            font-weight: 500;
            color: #495057;
            font-size: 0.9rem;
        }

        .user-dropdown .user-role {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            padding: 0.5rem;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: #f1f7fe;
            color: #3a7bd5;
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: #eee;
        }

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                margin-left: 0;
            }
        }

        /* Toggle Button */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: #495057;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        @media (max-width: 991.98px) {
            .sidebar-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <img src="{{ asset('images/images.png') }}" alt="Company Logo">
                <span>OCM</span>
            </a>
        </div>
        <ul class="sidebar-menu">
            <!-- Dashboard -->
            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Maintenance -->
            <li class="sidebar-item">
                <a href="#maintenanceSubmenu" class="sidebar-link" data-bs-toggle="collapse" role="button">
                    <i class="fas fa-tools"></i>
                    <span>Maintenance</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="sidebar-dropdown collapse {{ request()->routeIs('admin.tasks.*') || request()->routeIs('admin.locations.*') ? 'show' : '' }}" id="maintenanceSubmenu">
                    <li>
                        <a href="{{ route('admin.tasks.view') }}" class="sidebar-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <span>Manage Tasks</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.locations.index') }}" class="sidebar-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Manage Locations</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Users -->
            <li class="sidebar-item">
                <a href="#usersSubmenu" class="sidebar-link" data-bs-toggle="collapse" role="button">
                    <i class="fas fa-users-cog"></i>
                    <span>Users</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="sidebar-dropdown collapse {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.technicians.*') ? 'show' : '' }}" id="usersSubmenu">
                    <li>
                        <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <i class="fas fa-user-graduate"></i>
                            <span>View Student info</span>
                        </a>
                    </li>
                        <li>
                        <a href="{{ route('staff.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span>View Staff info</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.technicians.index') }}" class="sidebar-link {{ request()->routeIs('admin.technicians.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            <span>Manage Technicians</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Reports -->
            <li class="sidebar-item">
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>

               <li class="sidebar-item">
                <a href="{{ route('admin.feedbacks.index') }}" class="sidebar-link">
                    <i class="fa-solid fa-comments"></i>
                    <span>feedback given on tasks</span>
                </a>
            </li>

               <li class="sidebar-item">
                <a href="{{ route('notify.index') }}" class="sidebar-link">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
            </div>
            <div class="topbar-right">
                <!-- Notifications -->
                <div class="notification-badge">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        @auth
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        @endauth
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Notifications</h6>
                        @auth
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-bell text-primary"></i>
                                    <div>
                                        <p class="mb-0">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <div class="dropdown-item text-center text-muted">
                                    <i class="fas fa-bell-slash mb-2"></i>
                                    <p class="mb-0">No new notifications</p>
                                </div>
                            @endforelse
                        @endauth
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="{{ route('notify.index') }}">
                            <small>View all notifications</small>
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                @auth
                    <div class="dropdown">
                        <div class="user-dropdown" data-bs-toggle="dropdown">
                            <img src="{{ asset('images/default-avatar.png') }}" alt="User Avatar" onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent('{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}')+'&background=3a7bd5&color=fff'">
                            <div class="user-info">
                                <span class="user-name">{{ Auth::user()->first_name }}</span>
                                <span class="user-role">{{ ucfirst(Auth::user()->user_role) }}</span>
                            </div>
                            <i class="fas fa-chevron-down ms-2"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('adminProfile') }}">
                                    <i class="fas fa-user"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                           
                            <div class="dropdown-divider"></div>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login
                    </a>
                @endauth
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>

    @stack('scripts')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        });
    </script>

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
