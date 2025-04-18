<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class AuthController extends Controller
{


    // return the login view 
    public function showLoginForm()
    {
        return view('login');
    }

    // Handle login form submission
    public function login(Request $request)
{
    // Validate the request
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|in:Student,Technician,Admin',
    ]);

    // Find the user by email
    $user = User::where('email', $request->email)->first();
   

    // Check if the user exists
    if (!$user) {
        return back()->withErrors(['email' => 'Email not found.']);
    }

    // Check if the password is correct
    if (!Hash::check($request->password, $user->password_hash)) {
        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    // Check if the user's role matches the selected role
    if ($request->role !== $user->user_role ) {
        return back()->withErrors(['role' => 'Invalid role for this user.']);
    }

    // Log the user in
    Auth::login($user);

    // Redirect based on role
    switch ($request->role) {
        case 'Student':
            return redirect()->route('Student.dashboard');
        case 'Technician':
            return redirect()->route('technician.dashboard');
        case 'Admin':
            return redirect()->route('admin.dashboard');
        default:
            return redirect()->route('home'); // Fallback route
    }
}

    // Show password reset form (optional)
    public function showResetForm()
    {
        return view('reset-password');
    }
    
    public function sendResetLink(Request $request)
    {
        // Validate the email
        $request->validate(['email' => 'required|email']);
    
        // Check if the email exists in the database
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            // If the email does not exist, return an error
            return back()->withErrors(['email' => 'Email not found.']);
        }
    
        // Generate a password reset token
        $token = Str::random(60);
        $resetUrl = url('/reset-password/' . $token);
    
        // Send the email
        Mail::to($request->email)->send(new PasswordResetMail($resetUrl));
    
        return back()->with('status', 'Password reset link sent!');
    }


public function showResetPasswordForm($token)
{
    return view('auth.reset-password-form', ['token' => $token]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);
/*
view is blade.php files
routes -web.php
controller -http/App/Controllers/AuthController.php
Models - Http/App/Models


*/
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password_hash' => Hash::make($password), // Update the password
            ])->setRememberToken(Str::random(60));

            $user->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
}
}
