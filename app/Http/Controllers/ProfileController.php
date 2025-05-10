<?php

namespace App\Http\Controllers;

use App\Models\Admin;
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
    $roleData = null;

    // Check user role and fetch appropriate details
    if ($user->user_role === 'Student') {
        $roleData = DB::table('students')
            ->where('user_id', $user->user_id)
            ->select('student_number', 'course', 'faculty')
            ->first();
    } elseif ($user->user_role === 'Staff_Member') {
        $roleData = DB::table('staff_members')
            ->where('user_id', $user->user_id)
            ->select('department', 'position_title')
            ->first();
    }

    return view('Student.profile', [
        'user' => $user,
        'roleData' => $roleData
    ]);
}



    public function techProfile()
{
    // Fetch the authenticated user
    $user = Auth::user();

    // Fetch the maintenance staff details for the authenticated user
    $maintenanceStaff = DB::table('Technicians')
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
        $user = Auth::user();
        $admin = Admin::where('user_id', $user->user_id)->first();

        return view('Admin.profile', [
            'user' => $user,
            'admin' => $admin
        ]);
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
        $admin = Admin::where('user_id', $user->user_id)->first();

        // Get original values before update
        $original = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
        ];

        // Validate request
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Check if any fields actually changed
        $hasChanges = false;
        foreach ($validated as $field => $value) {
            if ($user->$field != $value) {
                $hasChanges = true;
                break;
            }
        }

        if (!$hasChanges) {
            return redirect()->route('adminEdit')
                ->with('info', 'No changes were made to your profile.');
        }

        // Update user details
        $user->update($validated);

        // Determine what changed
        $changes = [];
        foreach ($original as $field => $value) {
            $newValue = $user->$field;
            if ($field === 'phone_number' || $field === 'address') {
                $value = $value ?? 'not set';
                $newValue = $newValue ?? 'not set';
            }

            if ($value != $newValue) {
                $fieldName = str_replace('_', ' ', ucwords($field));
                $changes[] = "$fieldName changed from '$value' to '$newValue'";
            }
        }

        // Only notify if there are actual changes
        if (!empty($changes)) {
            $message = 'Your admin profile has been updated.';
            $message .= "\n\nChanges:\n• " . implode("\n• ", $changes);

            $user->notify(new DatabaseNotification(
                $message,
                route('adminProfile')
            ));
        }

        return redirect()->route('adminEdit')
            ->with('success', 'Profile updated successfully.');
    }


    public function techUpdate(Request $request)
    {
        $user = Auth::user();

        // Get original values before update
        $original = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
        ];

        // Validate request
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Check if any fields actually changed
        $hasChanges = false;
        foreach ($validated as $field => $value) {
            if ($user->$field != $value) {
                $hasChanges = true;
                break;
            }
        }

        if (!$hasChanges) {
            return redirect()->route('tech_edit')
                ->with('info', 'No changes were made to your profile.');
        }

        // Update user details
        $user->update($validated);

        // Determine what changed
        $changes = [];
        foreach ($original as $field => $value) {
            $newValue = $user->$field;
            if ($field === 'phone_number' || $field === 'address') {
                $value = $value ?? 'not set';
                $newValue = $newValue ?? 'not set';
            }

            if ($value != $newValue) {
                $fieldName = str_replace('_', ' ', ucwords($field));
                $changes[] = "$fieldName changed from '$value' to '$newValue'";
            }
        }

        // Only notify if there are actual changes
        if (!empty($changes)) {
            $message = 'Your technician profile has been updated.';
            $message .= "\n\nChanges:\n• " . implode("\n• ", $changes);

            $user->notify(new DatabaseNotification(
                $message,
                route('techProfile')
            ));
        }

        return redirect()->route('tech_edit')
            ->with('success', 'Profile updated successfully.');
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        // Get original values before update
        $original = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
        ];

        // Validate request
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Update user details
        $user->update($validated);

        // Determine what changed
        $changes = [];
        foreach ($original as $field => $value) {
            $newValue = $user->$field;
            if ($field === 'phone_number' || $field === 'address') {
                $value = $value ?? 'not set';
                $newValue = $newValue ?? 'not set';
            }

            if ($value != $newValue) {
                $fieldName = str_replace('_', ' ', ucwords($field));
                $changes[] = "$fieldName changed from '$value' to '$newValue'";
            }
        }


        if (!empty($changes)) {
            $message = 'Your profile has been updated.';
            $message .= "\n\nChanges:\n• " . implode("\n• ", $changes);

            $user->notify(new DatabaseNotification(
                $message,
                route('profile')
            ));
        }

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

        // Set session flash message for SweetAlert
        session()->flash('password_changed', true);

        // Redirect to login with a success message
        return redirect()->route('login');
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
