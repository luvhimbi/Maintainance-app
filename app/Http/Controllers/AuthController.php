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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{


    public function showLoginForm()
{
    // Check if user is already logged in
    if (Auth::check()) {
        
        switch (Auth::user()->user_role) {
            case 'Student':
            case 'Staff_Member':
                return redirect()->route('Student.dashboard'); // Combined dashboard
            case 'Technician':
                return redirect()->route('technician.dashboard');
            case 'Admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('home');
        }
    }

    return view('login');
}

public function showResetForm()
{
    return view('reset-password');
}

public function login(Request $request)
{
    // Validate the request
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|in:Student,Staff_Member,Technician,Admin', // Updated role options
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

   
    if ($request->role !== $user->user_role) {
        return back()->withErrors(['role' => 'Invalid role for this user.']);
    }

    // Log the user in
    Auth::login($user);

    // Redirect based on role
    switch ($request->role) {
        case 'Student':
        case 'Staff_Member':
            return redirect()->route('Student.dashboard'); 
        case 'Technician':
            return redirect()->route('technician.dashboard');
        case 'Admin':
            return redirect()->route('admin.dashboard');
        default:
            return redirect()->route('home');
    }
}





   public function sendResetLink(Request $request)
{
    // Validate the email
    $request->validate(['email' => 'required|email']);

    // Check if the email exists in the database
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'Email not found.']);
    }

    // Generate a token
    $token = Str::random(60);
    $hashedToken = Hash::make($token);


    // Store the token in the password_reset_tokens table
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $user->email],
        [
            'token' => $hashedToken, // Hashed token for security
            'created_at' => Carbon::now()
        ]
    );

   // Generate the reset URL using plain token
    $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($user->email));

    // Send the reset email
    Mail::to($user->email)->send(new PasswordResetMail($resetUrl));

    return back()->with('status', 'Reset password link sent. Please check your email!');
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

    // Get the reset record
    $reset = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();



    if (!$reset || !Hash::check($token, $reset->token)) {
        return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
    }

    // Update the user's password
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'No user found with this email address.']);
    }

    $user->password_hash = Hash::make($request->password);
    $user->setRememberToken(Str::random(60));
    $user->save();

    // Delete the password reset record
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('success', 'Your password has been reset successfully!');
}

}
