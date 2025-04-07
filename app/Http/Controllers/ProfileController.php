<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\ProfileUpdatedNotification;
use Illuminate\Support\Facades\Hash;
use App\Models\MaintenanceStaff;

class ProfileController extends Controller
{
    public function index()
    {
        return view('Student.profile', ['user' => Auth::user()]);
    }



    public function techProfile()
{
    // Fetch the authenticated user
    $user = Auth::user();

    // Fetch the maintenance staff details for the authenticated user
    $maintenanceStaff = DB::table('maintenance_staff')
        ->where('user_id', $user->user_id)
        ->first();

    // Pass both user and maintenance staff data to the view
    return view('Technician.profile', [
        'user' => $user,
        'maintenanceStaff' => $maintenanceStaff
    ]);
}
    public function adminProfile()
    {
        return view('Admin.profile', ['user' => Auth::user()]);
    }
    public function edit()
    {
        return view('Student.profileedit', ['user' => Auth::user()]);
    }

    public function editProfile(){
        return view('Technician.profileedit', ['user' => Auth::user()]);
    }


    public function adminEditProfile(){
        return view('admin.profileedit', ['user' => Auth::user()]);
    }


    public function adminUpdate(Request $request)
    {
        $user = Auth::user();
    
        // Validate request
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => 'nullable|string|max:15',
        ]);
    
        // Update user details
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
    
       
        $user->notify(new ProfileUpdatedNotification());
        return redirect()->route('adminEdit')
            ->with('success', 'Profile updated successfully.');
    }



    public function techUpdate(Request $request)
    {
        $user = Auth::user();
    
        // Validate request
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => 'nullable|string|max:15',
        ]);
    
        // Update user details
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
    
       
        $user->notify(new ProfileUpdatedNotification());
        return redirect()->route('tech_edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
    
        // Validate request
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => 'nullable|string|max:15',
        ]);
    
        // Update user details
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
    
       
        $user->notify(new ProfileUpdatedNotification());
        return redirect()->route('test.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }


    public function updatePassword(Request $request)
    {
        $user = Auth::user();
    
        // Validate request
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
    
        // Verify current password
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
    
        // Update password
        $user->update([
            'password_hash' => Hash::make($request->new_password),
        ]);
    
        // Logout the user
        Auth::logout();
    
        // Redirect to login with a success message
        return redirect()->route('login')->with('success', 'Your password has been updated successfully. Please login again.');
    }
}
