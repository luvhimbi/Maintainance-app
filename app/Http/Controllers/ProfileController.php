<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Hash;
use App\Models\MaintenanceStaff;


class ProfileController extends Controller
{
    public function index()
    {
        // Fetch the authenticated user
        $user = Auth::user();

        // Fetch the maintenance staff details for the authenticated user
        $campus_member= DB::table('campus_members')
            ->where('user_id', $user->user_id)
            ->first();

        return view('Student.profile', [
            'user' => Auth::user(),
             'campus_member' => $campus_member
        ]);
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


        $user->notify(new DatabaseNotification(
            'Your profile has been updated successfully',
            route('profile')
        ));
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

        $user->notify(new DatabaseNotification(
            'Your profile has been updated successfully',
            route('profile')
        ));
        return redirect()->route('tech_edit')
            ->with('success', 'Profile updated successfully.');
    }

    // public function update(Request $request)
    // {
    //     $user = Auth::user();

    //     // Validate request
    //     $request->validate([
    //         'username' => 'required|string|max:255',
    //         'email' => ['required', 'email', 'max:255'],
    //         'phone_number' => 'nullable|string|max:15',
    //     ]);

    //     // Update user details
    //     $user->update([
    //         'username' => $request->username,
    //         'email' => $request->email,
    //         'phone_number' => $request->phone_number,
    //     ]);


    //     $user->notify(new DatabaseNotification(
    //         'Your profile has been updated successfully',
    //         route('profile')
    //     ));
    //     return redirect()->route('test.profile.edit')
    //         ->with('success', 'Profile updated successfully.');
    // }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Get original values before update
        $original = [
            'username' => $user->username,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
        ];

        // Validate request
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required', 'email', 'max:255',
            'phone_number' => 'nullable|string|max:15',
        ]);

        // Update user details
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        // Determine what changed
        $changes = [];
        if ($original['username'] !== $user->username) {
            $changes[] = "Username changed from '{$original['username']}' to '{$user->username}'";
        }
        if ($original['email'] !== $user->email) {
            $changes[] = "Email changed from '{$original['email']}' to '{$user->email}'";
        }
        if ($original['phone_number'] != $user->phone_number) { // Using loose comparison for null
            $originalPhone = $original['phone_number'] ?? 'not set';
            $newPhone = $user->phone_number ?? 'not set';
            $changes[] = "Phone number changed from '{$originalPhone}' to '{$newPhone}'";
        }

        // Create detailed notification message
        $message = 'Your profile has been updated.';
        if (!empty($changes)) {
            $message .= "\n\nChanges:\n• " . implode("\n• ", $changes);
        } else {
            $message .= "\n\nNo fields were changed.";
        }

        $user->notify(new DatabaseNotification(
            $message,
            route('profile')
        ));

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

    public function bulkDestroy(Request $request)
{
    $request->validate([
        'notifications' => 'required|array',
        'notifications.*' => 'exists:notifications,id'
    ]);

    try {
        auth()->user()->notifications()
            ->whereIn('id', $request->notifications)
            ->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Selected notifications deleted successfully');

    } catch (\Exception $e) {
        return redirect()->route('notifications.index')
            ->with('error', 'Failed to delete notifications: ' . $e->getMessage());
    }
}
}
