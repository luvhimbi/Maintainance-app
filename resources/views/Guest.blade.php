<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCM - Home Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            font-size: 1.1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
        }

        .hero {
            background-color: #ffffff; /* white background */
            color: #000000; /* dark text for contrast */
            padding: 200px 0; /* makes hero section larger */
            text-align: center;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }

        .features, .how-it-works, .cta-section {
            padding: 80px 0;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
        }

        .step-number {
            font-size: 1.5rem;
            width: 60px;
            height: 60px;
            border: 3px solid #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            margin: 0 auto 1rem;
            font-weight: 600;
        }

        .cta-section {
            background-color: #f8f9fa;
            text-align: center;
        }

        footer {
            background-color: #f1f1f1;
        }

        .navbar {
            box-shadow: none !important;
            border-bottom: none !important;
        }
    </style>
</head>
<body>

@include('layouts.GuestNavBar')

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome to OCM</h1>
        <p class="mt-3">Easily report and track campus maintenance issues at Tshwane University of Technology.</p>
        <a href="/login" class="btn btn-primary btn-lg">Login to Report an Issue</a>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="features">
    <div class="container text-center">
        <h2 class="mb-5">Key Features</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-icon mb-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <h5>Quick Reporting</h5>
                <p>Log maintenance, safety, or infrastructure issues in seconds.</p>
            </div>
            <div class="col-md-4">
                <div class="feature-icon mb-3">
                    <i class="bi bi-map"></i>
                </div>
                <h5>Location Mapping</h5>
                <p>Pinpoint problem areas on an interactive campus map.</p>
            </div>
            <div class="col-md-4">
                <div class="feature-icon mb-3">
                    <i class="bi bi-bell"></i>
                </div>
                <h5>Email Alerts</h5>
                <p>Get notified when your issue is updated or resolved.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works" id="how-it-works">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row gy-5">
            <div class="col-md-4 text-center">
                <div class="step-number">1</div>
                <h5>Login</h5>
                <p>Log into the system using your student or staff credentials securely.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">2</div>
                <h5>Submit an Issue</h5>
                <p>Click “Report Issue”, describe the problem, choose an issue type and location, and submit an image if available.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">3</div>
                <h5>System Assignment</h5>
                <p>The system automatically assigns the issue to a technician based on category and location.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">4</div>
                <h5>Technician Updates</h5>
                <p>Technicians log their progress and status changes on the issue in real-time.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">5</div>
                <h5>Notifications</h5>
                <p>You receive notifications via email and app whenever your issue is updated.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">6</div>
                <h5>Issue Resolved</h5>
                <p>Once resolved, a report is available for review and feedback can be submitted.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call To Action Section -->
<section class="cta-section">
    <div class="container">
        <h3 class="fw-bold mb-3">Keep TUT Safe and Functional</h3>
        <p class="mb-4">If you see something broken or unsafe, don’t ignore it. Help improve your campus.</p>
        <a href="/login" class="btn btn-primary btn-lg">Login to Report</a>
    </div>
</section>

<!-- Footer -->
@include('Layouts.footer')

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
