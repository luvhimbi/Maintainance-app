<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaving Our Site</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .leave-container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 32px rgba(67,97,238,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        .leave-icon {
            font-size: 3.5rem;
            color: #4361ee;
            margin-bottom: 1rem;
        }
        .leave-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #22223b;
            margin-bottom: 0.5rem;
        }
        .leave-desc {
            color: #495057;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        .leave-url {
            background: #f4f6fa;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            color: #4361ee;
            word-break: break-all;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        .btn-leave {
            font-weight: 600;
            border-radius: 30px;
            padding: 0.75rem 2.5rem;
            font-size: 1.1rem;
            background: #4361ee;
            color: #fff;
            border: none;
            transition: background 0.2s;
        }
        .btn-leave:hover {
            background: #3a56d4;
            color: #fff;
        }
        .btn-cancel {
            margin-top: 1.2rem;
            color: #6c757d;
            text-decoration: underline;
            font-size: 1rem;
            background: none;
            border: none;
        }
        .btn-cancel:hover {
            color: #22223b;
        }
    </style>
</head>
<body>
    <div class="leave-container">
        <div class="leave-icon">
            <i class="bi bi-box-arrow-up-right"></i>
        </div>
        <div class="leave-title">You are about to leave our site</div>
        <div class="leave-desc">
            For your security, please confirm you want to continue to the following external link:
        </div>
        <div class="leave-url">
            {{ $url }}
        </div>
        <form action="{{ $url }}" method="get" class="mb-2">
            <button type="submit" class="btn btn-leave w-100">
                Continue to External Site <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>
        <a href="{{ url()->previous() }}" class="btn btn-cancel w-100">Cancel and go back</a>
    </div>
    <!-- Bootstrap 5 JS Bundle & Bootstrap Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</body>
</html>
