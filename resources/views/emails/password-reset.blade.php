<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset Request</h1>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Click the link below to reset your password:</p>
    <a href="{{ $resetUrl }}">Reset Password</a>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Thank you!</p>
</body>
</html>