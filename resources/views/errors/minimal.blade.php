<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
body{
    font-family: 'Poppins', sans-serif;
}
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-box {
            border-left: 4px solid #dee2e6;
            padding-left: 1rem;
        }

        .error-code {
            font-size: 2rem;
            font-weight: 600;
            color: #6c757d;
            border-right: 1px solid #dee2e6;
            padding-right: 1rem;
        }

        .error-message {
            font-size: 1.5rem;
            font-weight: 500;
            text-transform: uppercase;
            color: #6c757d;
            padding-left: 1rem;
        }

        .error-action {
            margin-top: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container error-container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="d-flex align-items-center justify-content-center error-box">
                <div class="error-code">
                    @yield('code')
                </div>
                <div class="error-message text-start">
                    @yield('message')
                </div>
            </div>

            <div class="error-action">
                @yield('action')
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

