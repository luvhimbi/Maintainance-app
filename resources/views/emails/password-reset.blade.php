<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - OCM</title>
    <style>
        body {
            background: #f4f6fa;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #22223b;
        }
        .email-container {
            max-width: 520px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(67,97,238,0.08);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .email-header {
            background: #4361ee;
            color: #fff;
            padding: 32px 32px 16px 32px;
            text-align: center;
        }
        .email-header img {
            height: 40px;
            margin-bottom: 12px;
        }
        .email-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }
        .email-content {
            padding: 32px;
            color: #22223b;
        }
        .email-content p {
            margin: 0 0 18px 0;
            font-size: 1rem;
            line-height: 1.6;
        }
        .reset-btn {
            display: inline-block;
            background: #4361ee;
            color: #fff !important;
            text-decoration: none;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 6px;
            margin: 18px 0;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: background 0.2s;
        }
        .reset-btn:hover {
            background: #3a56d4;
        }
        .email-footer {
            background: #f4f6fa;
            color: #6c757d;
            text-align: center;
            font-size: 0.95rem;
            padding: 18px 32px;
        }
        @media (max-width: 600px) {
            .email-container, .email-content, .email-header, .email-footer { padding: 16px !important; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="email-title">Password Reset Request</div>
        </div>
        <div class="email-content">
            <p>Hello,</p>
            <p>We received a request to reset your password for your <strong>OCM</strong> account.</p>
            <p>Click the button below to set a new password:</p>
            <p style="text-align:center;">
                <a href="{{ $resetUrl }}" class="reset-btn">Reset Password</a>
            </p>
            <p>If you did not request a password reset, you can safely ignore this email.</p>
            <p>For your security, this link will expire in 60 minutes.</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} OCM - Online Campus Management. All rights reserved.
        </div>
    </div>
</body>
</html>
