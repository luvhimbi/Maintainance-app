<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
            <img src="{{ asset('images/images.png') }}" alt="OCM Logo" width="40" class="me-2">
            <i class="fas fa-tools me-2"></i> OCM
        </a>

        @if (!Request::is('login') && !Request::is('reset-password') && !Request::is('reset-password/*'))
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-auto">
                    <li class="nav-item mx-3">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="{{ url('/login') }}" class="btn btn-primary btn-lg">Login</a>
                </div>
            </div>
        @endif
    </div>
</nav>
