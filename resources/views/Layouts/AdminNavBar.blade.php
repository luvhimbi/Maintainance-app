<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    
    <link rel="stylesheet" href="{{ asset('./css/global.css') }}">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}" >Dashboard</a>
                </li>
                <!-- Maintenance Tasks -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="tasksDropdown" role="button" data-bs-toggle="dropdown">
                        Maintenance Tasks
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="tasksDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.tasks.view') }}" >Manage Tasks</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.locations.index')}}" >Location(Qr code generator)</a></li>
                    </ul>
                </li>


                <!-- Reports -->
                <li class="nav-item dropdown">
                    <a class="nav-link " href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown">
                        Reports
                    </a>
                
                </li>

                <!-- User Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-bs-toggle="dropdown">
                        User Management
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="usersDropdown">
                        <li><a class="dropdown-item" href="{{route('admin.students.index')}}">Manage Students</a></li>
                        <li><a class="dropdown-item" href="{{route('admin.technicians.index')}}" >Manage Staff</a></li>

                    </ul>
                </li>



                <!-- Notifications -->
                <li class="nav-item position-relative">
                    <a class="nav-link">
                        Notifications
                        @auth
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                            @endif
                        @endauth
                    </a>
                </li>

                <!-- User Dropdown -->
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->username }} <!-- Display logged-in user's name -->
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" >Profile</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    @yield('content')
</div>

</body>
</html>
