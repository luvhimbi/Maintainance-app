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
    
  // Show password reset form (optional)
  public function showResetForm()
  {
      return view('reset-password');
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
    
        return back()->with('status', 'reset passwird link sent please follow the instructions sent to your email!');
    }


public function showResetPasswordForm($token)
{
    return view('reset-password-form', ['token' => $token]);
}

public function resetPassword(Request $request, $token)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);

    $status = Password::reset(
        [
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
            'token' => $token,
        ],
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();
        }
    );

    // Enhanced response handling
    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully!');
    }

    // More specific error messages
    $errorMessages = [
        Password::INVALID_TOKEN => 'The password reset token is invalid or has expired.',
        Password::INVALID_USER => 'We can\'t find a user with that email address.',
        Password::RESET_THROTTLED => 'Please wait before retrying.',
    ];

    $errorMessage = $errorMessages[$status] ?? 'An error occurred while resetting your password.';

    return back()
        ->withInput($request->only('email'))
        ->withErrors(['email' => $errorMessage]);
}
}
