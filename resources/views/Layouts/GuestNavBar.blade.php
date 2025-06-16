<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm" style="font-family: 'Poppins', sans-serif;">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#" style="font-size: 1.5rem; color: #2563eb;">
            <x-cloudinary-image 
                public-id="images" 
                alt="OCM Logo"
                class="me-2"
                style="height: 30px; width: auto;"
            />
            <i class="fas fa-tools me-2"></i>OCM
        </a>
        @if (!Request::is('login') && !Request::is('reset-password') && !Request::is('reset-password/*'))
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-auto">
                    <li class="nav-item mx-3">
                        <a class="nav-link active" href="#" style="font-weight: 500; color: #4b5563; padding: 0.5rem 1rem; transition: all 0.3s ease; border-radius: 0.5rem;">Home</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="#features" style="font-weight: 500; color: #4b5563; padding: 0.5rem 1rem; transition: all 0.3s ease; border-radius: 0.5rem;">Features</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="#how-it-works" style="font-weight: 500; color: #4b5563; padding: 0.5rem 1rem; transition: all 0.3s ease; border-radius: 0.5rem;">How It Works</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="{{ url('/login') }}" class="btn btn-primary btn-lg rounded-pill px-4" style="font-weight: 500;">Login</a>
                </div>
            </div>
        @endif
    </div>
</nav>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Only rewrite external links, not mailto: or tel:
    const links = document.querySelectorAll("a[href^='http']");
    const currentHost = window.location.hostname;

    links.forEach(link => {
        try {
            const url = new URL(link.href);
            if (
                url.hostname !== currentHost &&
                !link.href.startsWith('mailto:') &&
                !link.href.startsWith('tel:')
            ) {
                const encodedUrl = encodeURIComponent(link.href);
                link.setAttribute('data-leaving', 'true');
                link.href = `/leaving?url=${encodedUrl}`;
            }
        } catch (e) {
            // Ignore invalid URLs
        }
    });
});
</script>