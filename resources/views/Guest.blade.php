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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .navbar {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            font-size: 1.1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            background: #fff;
        }

        .navbar-brand {
            font-size: 1.7rem;
            font-weight: 700;
            color: #4361ee !important;
            letter-spacing: 1px;
        }

        .hero {
            background: linear-gradient(120deg, #4361ee 0%, #5f7cff 100%);
            color: #fff;
            padding: 140px 0 100px 0;
            text-align: center;
            border-radius: 0 0 60px 60px;
            position: relative;
            overflow: hidden;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .hero p {
            font-size: 1.4rem;
            margin-bottom: 32px;
            font-weight: 400;
        }

        .hero .btn-primary {
            font-size: 1.2rem;
            padding: 14px 40px;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(67,97,238,0.13);
            transition: background 0.2s;
        }
        .hero .btn-primary:hover {
            background: #3a56d4;
        }

        .features, .how-it-works, .cta-section {
            padding: 80px 0;
        }

        .feature-icon {
            font-size: 2.7rem;
            color: #4361ee;
            background: #f4f6fa;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            box-shadow: 0 2px 8px rgba(67,97,238,0.07);
        }

        .features h2 {
            font-weight: 700;
            color: #22223b;
        }

        .features h5 {
            font-weight: 600;
            color: #4361ee;
        }

        .step-number {
            font-size: 1.5rem;
            width: 60px;
            height: 60px;
            border: 3px solid #4361ee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4361ee;
            margin: 0 auto 1rem;
            font-weight: 600;
            background: #fff;
        }

        .how-it-works h2 {
            font-weight: 700;
            color: #22223b;
        }

        .cta-section {
            background: linear-gradient(120deg, #f4f6fa 0%, #e9ecef 100%);
            text-align: center;
            border-radius: 40px;
            margin-bottom: 40px;
        }

        .cta-section h3 {
            color: #22223b;
            font-weight: 700;
        }

        .cta-section .btn-primary {
            font-size: 1.1rem;
            padding: 12px 36px;
            border-radius: 30px;
            font-weight: 600;
        }

        .social-links {
            margin-top: 32px;
            display: flex;
            justify-content: center;
            gap: 18px;
        }
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff;
            color: #4361ee;
            font-size: 1.4rem;
            box-shadow: 0 2px 8px rgba(67,97,238,0.07);
            transition: background 0.2s, color 0.2s;
            border: 1px solid #e0e0e0;
        }
        .social-links a:hover {
            background: #4361ee;
            color: #fff;
        }

        footer {
            background: #22223b;
            color: #fff;
            padding: 40px 0 20px 0;
            text-align: center;
            border-radius: 40px 40px 0 0;
        }
        .footer-links {
            margin-bottom: 18px;
        }
        .footer-links a {
            color: #bfc6e0;
            margin: 0 12px;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: #fff;
        }
        .footer-social {
            margin-bottom: 18px;
        }
        .footer-social a {
            color: #fff;
            margin: 0 8px;
            font-size: 1.3rem;
            transition: color 0.2s;
        }
        .footer-social a:hover {
            color: #4361ee;
        }
        .footer-copyright {
            font-size: 0.95rem;
            color: #bfc6e0;
        }

        @media (max-width: 600px) {
            .hero h1 { font-size: 2.1rem; }
            .hero { padding: 80px 0 60px 0; }
            .features, .how-it-works, .cta-section { padding: 40px 0; }
            .cta-section { border-radius: 20px; }
            footer { border-radius: 20px 20px 0 0; }
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
        <div class="social-links mt-4">
            <a href="https://facebook.com/ocm-campus" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://twitter.com/ocm-campus" target="_blank" title="Twitter"><i class="bi bi-twitter"></i></a>
            <a href="https://instagram.com/ocm-campus" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="mailto:support@ocm-campus.com" title="Email"><i class="bi bi-envelope"></i></a>
        </div>
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
        <div class="social-links mt-4">
            <a href="https://facebook.com/ocm-campus" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://twitter.com/ocm-campus" target="_blank" title="Twitter"><i class="bi bi-twitter"></i></a>
            <a href="https://instagram.com/ocm-campus" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="mailto:support@ocm-campus.com" title="Email"><i class="bi bi-envelope"></i></a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="footer-links mb-2">
        <a href="#">About</a>
        <a href="#">Contact</a>
        <a href="#">Help Center</a>
        <a href="#">Terms</a>
        <a href="#">Privacy</a>
    </div>
    <div class="footer-social mb-2">
        <a href="https://facebook.com/ocm-campus" target="_blank"><i class="bi bi-facebook"></i></a>
        <a href="https://twitter.com/ocm-campus" target="_blank"><i class="bi bi-twitter"></i></a>
        <a href="https://instagram.com/ocm-campus" target="_blank"><i class="bi bi-instagram"></i></a>
        <a href="mailto:support@ocm-campus.com"><i class="bi bi-envelope"></i></a>
        <a href="#"><i class="bi bi-linkedin"></i></a>
    </div>
    <div class="footer-copyright">
        &copy; {{ date('Y') }} OCM - Online Campus Management. All rights reserved.
    </div>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
